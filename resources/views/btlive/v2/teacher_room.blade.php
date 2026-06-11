<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $session->title }} - Teacher Room</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    
    <!-- LiveKit SDK -->
    <script src="https://cdn.jsdelivr.net/npm/livekit-client@1.15.0/dist/livekit-client.umd.min.js"></script>
    
    <style>
        .whiteboard-layer { position: absolute; top: 0; left: 0; pointer-events: none; }
        .annotation-active .whiteboard-layer { pointer-events: auto; }
        .raised-hand { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
    </style>
</head>
<body class="bg-gray-900 h-screen flex flex-col overflow-hidden">
    <!-- Header -->
    <header class="bg-gray-800 text-white px-4 py-2 flex items-center justify-between border-b border-gray-700">
        <div class="flex items-center gap-4">
            <h1 class="font-semibold text-lg">{{ $session->title }}</h1>
            <span id="live-badge" class="bg-red-500 text-white text-xs px-2 py-1 rounded font-bold animate-pulse">LIVE</span>
            <span class="text-sm text-gray-400"><span id="participant-count">{{ $session->participant_count }}</span> students</span>
        </div>
        <div class="flex items-center gap-2">
            <button id="btn-mute-all" class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-sm">Mute All</button>
            <button id="btn-block-all" class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-sm">Block Screens</button>
            <button id="btn-end-class" class="bg-red-600 hover:bg-red-700 px-4 py-1 rounded text-sm font-medium">End Class</button>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Left: Teacher Video & PDF -->
        <div class="flex-1 flex flex-col">
            <!-- Teacher Video Bar -->
            <div class="h-48 bg-gray-800 border-b border-gray-700 flex items-center justify-center relative">
                <video id="teacher-video" autoplay playsinline muted class="h-full w-auto max-w-full"></video>
                <div class="absolute bottom-2 left-2 flex gap-2">
                    <button id="btn-camera" class="bg-gray-700 hover:bg-gray-600 p-2 rounded-full">📹</button>
                    <button id="btn-mic" class="bg-gray-700 hover:bg-gray-600 p-2 rounded-full">🎤</button>
                    <button id="btn-screen" class="bg-gray-700 hover:bg-gray-600 p-2 rounded-full">🖥️</button>
                </div>
            </div>

            <!-- PDF Display Area -->
            <div class="flex-1 bg-gray-700 relative flex items-center justify-center overflow-hidden">
                <canvas id="pdf-canvas" class="max-w-full max-h-full shadow-lg"></canvas>
                <canvas id="whiteboard-canvas" class="whiteboard-layer"></canvas>
                
                <!-- PDF Controls -->
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex items-center gap-2 bg-gray-800 px-4 py-2 rounded-lg">
                    <button id="btn-prev-page" class="text-white hover:bg-gray-700 px-2 py-1 rounded">◀</button>
                    <span class="text-white text-sm">Page <span id="current-page">1</span> / <span id="total-pages">-</span></span>
                    <button id="btn-next-page" class="text-white hover:bg-gray-700 px-2 py-1 rounded">▶</button>
                </div>
            </div>

            <!-- Whiteboard Tools -->
            <div class="h-14 bg-gray-800 border-t border-gray-700 flex items-center px-4 gap-2">
                <span class="text-gray-400 text-sm mr-2">Tools:</span>
                <button class="tool-btn bg-blue-600 text-white px-3 py-1 rounded text-sm" data-tool="pen">Pen</button>
                <button class="tool-btn bg-gray-700 text-white px-3 py-1 rounded text-sm" data-tool="highlighter">Highlighter</button>
                <button class="tool-btn bg-gray-700 text-white px-3 py-1 rounded text-sm" data-tool="arrow">Arrow</button>
                <button class="tool-btn bg-gray-700 text-white px-3 py-1 rounded text-sm" data-tool="rectangle">Rect</button>
                <button class="tool-btn bg-gray-700 text-white px-3 py-1 rounded text-sm" data-tool="circle">Circle</button>
                <button class="tool-btn bg-gray-700 text-white px-3 py-1 rounded text-sm" data-tool="text">Text</button>
                <button class="tool-btn bg-gray-700 text-white px-3 py-1 rounded text-sm" data-tool="eraser">Eraser</button>
                <div class="border-l border-gray-600 mx-2 h-6"></div>
                <button id="btn-clear-whiteboard" class="bg-red-700 text-white px-3 py-1 rounded text-sm">Clear</button>
                <div class="flex-1"></div>
                <input type="color" id="color-picker" value="#ff0000" class="w-8 h-8 rounded">
                <input type="range" id="width-slider" min="1" max="20" value="3" class="w-24">
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="w-80 bg-gray-800 border-l border-gray-700 flex flex-col">
            <!-- Tabs -->
            <div class="flex border-b border-gray-700">
                <button class="tab-btn flex-1 py-2 text-sm text-white border-b-2 border-blue-500" data-tab="students">Students</button>
                <button class="tab-btn flex-1 py-2 text-sm text-gray-400" data-tab="chat">Chat</button>
                <button class="tab-btn flex-1 py-2 text-sm text-gray-400" data-tab="polls">Polls</button>
                <button class="tab-btn flex-1 py-2 text-sm text-gray-400" data-tab="pdfs">PDFs</button>
            </div>

            <!-- Students Tab -->
            <div id="tab-students" class="tab-content flex-1 overflow-y-auto p-3">
                <div class="mb-3">
                    <h3 class="text-gray-400 text-xs uppercase font-bold mb-2">Raised Hands</h3>
                    <div id="raised-hands-list" class="space-y-2">
                        @foreach($raisedHands as $hand)
                        <div class="bg-yellow-900/50 border border-yellow-700 rounded p-2 flex items-center justify-between raised-hand" data-hand-id="{{ $hand->id }}">
                            <span class="text-yellow-200 text-sm">{{ $hand->participant->name }}</span>
                            <div class="flex gap-1">
                                <button class="accept-hand bg-green-600 text-white px-2 py-1 rounded text-xs" data-hand-id="{{ $hand->id }}">Accept</button>
                                <button class="reject-hand bg-red-600 text-white px-2 py-1 rounded text-xs" data-hand-id="{{ $hand->id }}">Reject</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div>
                    <h3 class="text-gray-400 text-xs uppercase font-bold mb-2">All Students ({{ $participants->count() }})</h3>
                    <div id="participants-list" class="space-y-1">
                        @foreach($participants as $p)
                        <div class="flex items-center justify-between py-1 px-2 rounded hover:bg-gray-700" data-participant-id="{{ $p->id }}">
                            <span class="text-gray-300 text-sm">{{ $p->name }}</span>
                            <div class="flex gap-1">
                                @if($p->is_muted)<span class="text-xs text-gray-500">🔇</span>@endif
                                <button class="kick-participant text-red-400 hover:text-red-300 text-xs" data-id="{{ $p->id }}">Remove</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Chat Tab -->
            <div id="tab-chat" class="tab-content flex-1 flex flex-col hidden">
                <div id="chat-messages" class="flex-1 overflow-y-auto p-3 space-y-2">
                    @foreach($recentChat as $msg)
                    <div class="{{ $msg->message_type === 'teacher' ? 'bg-blue-900/50' : 'bg-gray-700' }} rounded p-2">
                        <div class="text-xs {{ $msg->message_type === 'teacher' ? 'text-blue-300' : 'text-gray-400' }}">
                            {{ $msg->participant?->name ?? 'System' }}
                        </div>
                        <div class="text-gray-200 text-sm">{{ $msg->content }}</div>
                    </div>
                    @endforeach
                </div>
                <div class="p-3 border-t border-gray-700">
                    <div class="flex gap-2">
                        <input type="text" id="chat-input" class="flex-1 bg-gray-700 text-white rounded px-3 py-2 text-sm" placeholder="Type message...">
                        <button id="btn-send-chat" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Send</button>
                    </div>
                </div>
            </div>

            <!-- Polls Tab -->
            <div id="tab-polls" class="tab-content flex-1 overflow-y-auto p-3 hidden">
                <button id="btn-create-poll" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded mb-3">+ Create Poll</button>
                
                <div id="active-polls">
                    @foreach($activePolls as $poll)
                    <div class="bg-gray-700 rounded p-3 mb-2" data-poll-id="{{ $poll->id }}">
                        <p class="text-white text-sm font-medium mb-2">{{ $poll->question }}</p>
                        <div class="space-y-1 mb-2">
                            @foreach($poll->options as $i => $opt)
                            <div class="bg-gray-600 rounded px-2 py-1 text-sm text-gray-300">{{ chr(65+$i) }}. {{ $opt }}</div>
                            @endforeach
                        </div>
                        <div class="flex gap-2">
                            <button class="close-poll bg-red-600 text-white px-3 py-1 rounded text-xs" data-poll-id="{{ $poll->id }}">Close</button>
                            <button class="reveal-poll bg-blue-600 text-white px-3 py-1 rounded text-xs" data-poll-id="{{ $poll->id }}">Reveal</button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Create Poll Form -->
                <div id="poll-form" class="hidden bg-gray-700 rounded p-3">
                    <input type="text" id="poll-question" class="w-full bg-gray-600 text-white rounded px-3 py-2 mb-2 text-sm" placeholder="Question">
                    <div id="poll-options" class="space-y-2 mb-2">
                        <input type="text" class="poll-option w-full bg-gray-600 text-white rounded px-3 py-2 text-sm" placeholder="Option 1">
                        <input type="text" class="poll-option w-full bg-gray-600 text-white rounded px-3 py-2 text-sm" placeholder="Option 2">
                    </div>
                    <button id="btn-add-option" class="text-blue-400 text-sm mb-2">+ Add option</button>
                    <div class="flex gap-2">
                        <button id="btn-launch-poll" class="flex-1 bg-green-600 text-white py-2 rounded text-sm">Launch</button>
                        <button id="btn-cancel-poll" class="flex-1 bg-gray-600 text-white py-2 rounded text-sm">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- PDFs Tab -->
            <div id="tab-pdfs" class="tab-content flex-1 overflow-y-auto p-3 hidden">
                <div class="mb-3">
                    <input type="file" id="pdf-upload" accept=".pdf" class="hidden">
                    <button id="btn-upload-pdf" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">+ Upload PDF</button>
                </div>
                <div id="pdfs-list" class="space-y-2">
                    @foreach($pdfs as $pdf)
                    <div class="flex items-center justify-between bg-gray-700 rounded p-2 {{ $pdf->is_active ? 'border-2 border-blue-500' : '' }}" data-pdf-id="{{ $pdf->id }}">
                        <span class="text-gray-300 text-sm truncate">{{ $pdf->title }}</span>
                        <button class="activate-pdf bg-blue-600 text-white px-2 py-1 rounded text-xs" data-pdf-id="{{ $pdf->id }}">Show</button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuration
        const SESSION_ID = {{ $session->id }};
        const ROOM_NAME = '{{ $session->room_name }}';
        const WS_URL = '{{ env('BTLIVE_WEBSOCKET_URL', 'wss://meet.btguru.tech') }}';
        
        // State
        let currentPdf = null;
        let currentPage = 1;
        let pdfDoc = null;
        let currentTool = 'pen';
        let isDrawing = false;
        let wsConnection = null;
        
        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            initWebSocket();
            initTabs();
            initTools();
            initWhiteboard();
            initEventListeners();
            
            // Load first PDF if exists
            const firstPdfBtn = document.querySelector('.activate-pdf');
            if (firstPdfBtn) {
                firstPdfBtn.click();
            }
        });

        // WebSocket Connection
        function initWebSocket() {
            // Using polling fallback for now - WebSocket would connect to LiveKit
            setInterval(pollState, 3000);
        }

        async function pollState() {
            try {
                const response = await fetch(`/btlive-v2/session/${SESSION_ID}/state?last_timestamp=${Date.now() - 5000}`);
                const data = await response.json();
                updateUI(data);
            } catch (e) {
                console.error('Poll error:', e);
            }
        }

        function updateUI(data) {
            document.getElementById('participant-count').textContent = data.state.participant_count;
        }

        // Tab Switching
        function initTabs() {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const tab = btn.dataset.tab;
                    document.querySelectorAll('.tab-btn').forEach(b => {
                        b.classList.remove('border-b-2', 'border-blue-500', 'text-white');
                        b.classList.add('text-gray-400');
                    });
                    btn.classList.add('border-b-2', 'border-blue-500', 'text-white');
                    btn.classList.remove('text-gray-400');
                    
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
                    document.getElementById(`tab-${tab}`).classList.remove('hidden');
                });
            });
        }

        // Tools
        function initTools() {
            document.querySelectorAll('.tool-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    currentTool = btn.dataset.tool;
                    document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('bg-blue-600'));
                    btn.classList.add('bg-blue-600');
                });
            });
        }

        // Whiteboard
        function initWhiteboard() {
            const canvas = document.getElementById('whiteboard-canvas');
            const ctx = canvas.getContext('2d');
            
            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            
            function startDrawing(e) {
                isDrawing = true;
                const rect = canvas.getBoundingClientRect();
                ctx.beginPath();
                ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
            }
            
            function draw(e) {
                if (!isDrawing) return;
                const rect = canvas.getBoundingClientRect();
                ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
                ctx.strokeStyle = document.getElementById('color-picker').value;
                ctx.lineWidth = document.getElementById('width-slider').value;
                ctx.lineCap = 'round';
                ctx.stroke();
            }
            
            function stopDrawing() {
                if (!isDrawing) return;
                isDrawing = false;
                ctx.closePath();
                
                // Broadcast annotation
                broadcastEvent('annotation', {
                    tool: currentTool,
                    tool_config: {
                        color: document.getElementById('color-picker').value,
                        width: document.getElementById('width-slider').value,
                    },
                    pdf_id: currentPdf?.id,
                    page_number: currentPage,
                });
            }
        }

        // Event Listeners
        function initEventListeners() {
            // PDF navigation
            document.getElementById('btn-prev-page').addEventListener('click', () => changePage(-1));
            document.getElementById('btn-next-page').addEventListener('click', () => changePage(1));
            
            // Whiteboard clear
            document.getElementById('btn-clear-whiteboard').addEventListener('click', () => {
                const canvas = document.getElementById('whiteboard-canvas');
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                broadcastEvent('whiteboard_clear', {
                    pdf_id: currentPdf?.id,
                    page_number: currentPage,
                });
            });
            
            // End class
            document.getElementById('btn-end-class').addEventListener('click', async () => {
                if (!confirm('Are you sure you want to end the class?')) return;
                await fetch(`/btlive-v2/session/${SESSION_ID}/end`, { method: 'POST' });
                window.location.href = '/admin/dashboard';
            });
            
            // Mute all
            document.getElementById('btn-mute-all').addEventListener('click', () => {
                broadcastEvent('mute_all', {});
            });
            
            // Chat
            document.getElementById('btn-send-chat').addEventListener('click', sendChat);
            document.getElementById('chat-input').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') sendChat();
            });
            
            // Poll creation
            document.getElementById('btn-create-poll').addEventListener('click', () => {
                document.getElementById('poll-form').classList.remove('hidden');
            });
            document.getElementById('btn-cancel-poll').addEventListener('click', () => {
                document.getElementById('poll-form').classList.add('hidden');
            });
            document.getElementById('btn-add-option').addEventListener('click', () => {
                const div = document.createElement('div');
                div.innerHTML = `<input type="text" class="poll-option w-full bg-gray-600 text-white rounded px-3 py-2 text-sm" placeholder="Option">`;
                document.getElementById('poll-options').appendChild(div.firstElementChild);
            });
            document.getElementById('btn-launch-poll').addEventListener('click', launchPoll);
            
            // PDF upload
            document.getElementById('btn-upload-pdf').addEventListener('click', () => {
                document.getElementById('pdf-upload').click();
            });
            document.getElementById('pdf-upload').addEventListener('change', uploadPdf);
            
            // Activate PDF
            document.querySelectorAll('.activate-pdf').forEach(btn => {
                btn.addEventListener('click', () => activatePdf(btn.dataset.pdfId));
            });
            
            // Raised hands
            document.querySelectorAll('.accept-hand').forEach(btn => {
                btn.addEventListener('click', () => handleHand(btn.dataset.handId, 'accept'));
            });
            document.querySelectorAll('.reject-hand').forEach(btn => {
                btn.addEventListener('click', () => handleHand(btn.dataset.handId, 'reject'));
            });
            
            // Kick participant
            document.querySelectorAll('.kick-participant').forEach(btn => {
                btn.addEventListener('click', () => kickParticipant(btn.dataset.id));
            });
        }

        // Helper Functions
        function changePage(delta) {
            if (!pdfDoc) return;
            const newPage = currentPage + delta;
            if (newPage >= 1 && newPage <= pdfDoc.numPages) {
                currentPage = newPage;
                renderPage(currentPage);
                broadcastEvent('pdf_page_change', {
                    pdf_id: currentPdf?.id,
                    page_number: currentPage,
                });
            }
        }

        function renderPage(pageNum) {
            pdfDoc.getPage(pageNum).then(page => {
                const canvas = document.getElementById('pdf-canvas');
                const ctx = canvas.getContext('2d');
                const viewport = page.getViewport({ scale: 1.5 });
                
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                page.render({ canvasContext: ctx, viewport });
                
                // Update whiteboard canvas size
                const wbCanvas = document.getElementById('whiteboard-canvas');
                wbCanvas.width = viewport.width;
                wbCanvas.height = viewport.height;
                
                document.getElementById('current-page').textContent = pageNum;
                document.getElementById('total-pages').textContent = pdfDoc.numPages;
            });
        }

        async function activatePdf(pdfId) {
            const response = await fetch(`/btlive-v2/session/${SESSION_ID}/activate-pdf`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ pdf_id: pdfId, page: 1 }),
            });
            const data = await response.json();
            
            currentPdf = data.pdf;
            currentPage = 1;
            
            // Load PDF
            const loadingTask = pdfjsLib.getDocument(data.pdf.url);
            loadingTask.promise.then(pdf => {
                pdfDoc = pdf;
                renderPage(1);
            });
            
            // Update UI
            document.querySelectorAll('#pdfs-list > div').forEach(div => {
                div.classList.remove('border-2', 'border-blue-500');
            });
            document.querySelector(`[data-pdf-id="${pdfId}"]`).classList.add('border-2', 'border-blue-500');
        }

        async function uploadPdf(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const formData = new FormData();
            formData.append('pdf', file);
            formData.append('title', file.name);
            
            const response = await fetch(`/btlive-v2/session/${SESSION_ID}/upload-pdf`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData,
            });
            
            const data = await response.json();
            
            // Add to list
            const div = document.createElement('div');
            div.className = 'flex items-center justify-between bg-gray-700 rounded p-2';
            div.dataset.pdfId = data.pdf_id;
            div.innerHTML = `
                <span class="text-gray-300 text-sm truncate">${data.title}</span>
                <button class="activate-pdf bg-blue-600 text-white px-2 py-1 rounded text-xs" data-pdf-id="${data.pdf_id}">Show</button>
            `;
            document.getElementById('pdfs-list').appendChild(div);
            
            div.querySelector('.activate-pdf').addEventListener('click', () => activatePdf(data.pdf_id));
        }

        async function launchPoll() {
            const question = document.getElementById('poll-question').value;
            const options = Array.from(document.querySelectorAll('.poll-option')).map(i => i.value).filter(v => v);
            
            if (!question || options.length < 2) {
                alert('Please enter a question and at least 2 options');
                return;
            }
            
            const response = await fetch(`/btlive-v2/session/${SESSION_ID}/teacher-event`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    event_type: 'poll_start',
                    data: { question, options },
                }),
            });
            
            const data = await response.json();
            
            // Add to active polls
            const pollDiv = document.createElement('div');
            pollDiv.className = 'bg-gray-700 rounded p-3 mb-2';
            pollDiv.dataset.pollId = data.poll.id;
            pollDiv.innerHTML = `
                <p class="text-white text-sm font-medium mb-2">${question}</p>
                <div class="space-y-1 mb-2">
                    ${options.map((opt, i) => `<div class="bg-gray-600 rounded px-2 py-1 text-sm text-gray-300">${String.fromCharCode(65+i)}. ${opt}</div>`).join('')}
                </div>
                <div class="flex gap-2">
                    <button class="close-poll bg-red-600 text-white px-3 py-1 rounded text-xs" data-poll-id="${data.poll.id}">Close</button>
                    <button class="reveal-poll bg-blue-600 text-white px-3 py-1 rounded text-xs" data-poll-id="${data.poll.id}">Reveal</button>
                </div>
            `;
            document.getElementById('active-polls').appendChild(pollDiv);
            
            document.getElementById('poll-form').classList.add('hidden');
            document.getElementById('poll-question').value = '';
            document.querySelectorAll('.poll-option').forEach(i => i.value = '');
        }

        async function sendChat() {
            const input = document.getElementById('chat-input');
            const content = input.value.trim();
            if (!content) return;
            
            input.value = '';
            
            await fetch(`/btlive-v2/session/${SESSION_ID}/teacher-event`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    event_type: 'chat_message',
                    data: { content },
                }),
            });
        }

        async function handleHand(handId, action) {
            await fetch(`/btlive-v2/session/${SESSION_ID}/teacher-event`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    event_type: action === 'accept' ? 'accept_hand' : 'reject_hand',
                    data: { hand_id: handId, teacher_id: {{ Auth::id() }} },
                }),
            });
            
            document.querySelector(`[data-hand-id="${handId}"]`).remove();
        }

        async function kickParticipant(participantId) {
            if (!confirm('Remove this participant?')) return;
            
            await fetch(`/btlive-v2/session/${SESSION_ID}/teacher-event`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    event_type: 'kick_participant',
                    data: { participant_id: participantId },
                }),
            });
            
            document.querySelector(`[data-participant-id="${participantId}"]`).remove();
        }

        async function broadcastEvent(eventType, data) {
            await fetch(`/btlive-v2/session/${SESSION_ID}/teacher-event`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ event_type: eventType, data }),
            });
        }
    </script>
</body>
</html>
