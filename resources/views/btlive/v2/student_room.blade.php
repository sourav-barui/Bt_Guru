<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $session->title }} - Student</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    
    <style>
        .whiteboard-layer { position: absolute; top: 0; left: 0; pointer-events: none; }
        .participant-card { transition: all 0.3s ease; }
    </style>
</head>
<body class="bg-gray-900 h-screen flex flex-col overflow-hidden">
    <!-- Header -->
    <header class="bg-gray-800 text-white px-4 py-3 flex items-center justify-between border-b border-gray-700">
        <div class="flex items-center gap-4">
            <h1 class="font-semibold text-lg">{{ $session->title }}</h1>
            <span id="live-badge" class="bg-red-500 text-white text-xs px-2 py-1 rounded font-bold animate-pulse">LIVE</span>
        </div>
        <div class="flex items-center gap-2">
            <button id="btn-hand" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-full text-sm font-medium flex items-center gap-2">
                <span>✋</span>
                <span>Raise Hand</span>
            </button>
            <button id="btn-leave" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-medium">Leave</button>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
        <!-- Left: Video & Content -->
        <div class="flex-1 flex flex-col">
            <!-- Teacher Video -->
            <div class="h-48 lg:h-56 bg-gray-800 border-b border-gray-700 flex items-center justify-center relative">
                <video id="teacher-video" autoplay playsinline class="h-full w-full object-contain"></video>
                <div id="video-placeholder" class="absolute inset-0 flex items-center justify-center bg-gray-800">
                    <div class="text-center text-gray-400">
                        <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-2">
                            <span class="text-3xl">👨‍🏫</span>
                        </div>
                        <p class="text-sm">Teacher video</p>
                    </div>
                </div>
            </div>

            <!-- PDF / Whiteboard Area -->
            <div class="flex-1 bg-gray-700 relative flex items-center justify-center overflow-hidden p-4">
                <div id="content-container" class="relative max-w-full max-h-full">
                    <canvas id="pdf-canvas" class="shadow-lg max-w-full max-h-full"></canvas>
                    <canvas id="whiteboard-canvas" class="whiteboard-layer"></canvas>
                </div>
                
                <!-- Page indicator -->
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-gray-800 px-4 py-2 rounded-lg text-white text-sm">
                    Page <span id="current-page">1</span> / <span id="total-pages">-</span>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="h-64 lg:h-auto lg:w-80 bg-gray-800 border-l border-gray-700 flex flex-col">
            <!-- Tabs -->
            <div class="flex border-b border-gray-700">
                <button class="tab-btn flex-1 py-3 text-sm text-white border-b-2 border-blue-500 font-medium" data-tab="chat">Chat</button>
                <button class="tab-btn flex-1 py-3 text-sm text-gray-400" data-tab="polls">Polls</button>
                <button class="tab-btn flex-1 py-3 text-sm text-gray-400" data-tab="participants">People</button>
            </div>

            <!-- Chat Tab -->
            <div id="tab-chat" class="tab-content flex-1 flex flex-col">
                <div id="chat-messages" class="flex-1 overflow-y-auto p-3 space-y-2">
                    @if($currentState['recent_chat'])
                        @foreach($currentState['recent_chat'] as $msg)
                        <div class="{{ $msg->message_type === 'teacher' ? 'bg-blue-900/50' : 'bg-gray-700' }} rounded p-2">
                            <div class="text-xs {{ $msg->message_type === 'teacher' ? 'text-blue-300' : 'text-gray-400' }}">
                                {{ $msg->participant?->name ?? 'System' }}
                            </div>
                            <div class="text-gray-200 text-sm">{{ $msg->content }}</div>
                        </div>
                        @endforeach
                    @endif
                </div>
                <div class="p-3 border-t border-gray-700 bg-gray-800">
                    <div class="flex gap-2">
                        <input type="text" id="chat-input" 
                            class="flex-1 bg-gray-700 text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            placeholder="Type a message...">
                        <button id="btn-send" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                            Send
                        </button>
                    </div>
                </div>
            </div>

            <!-- Polls Tab -->
            <div id="tab-polls" class="tab-content flex-1 overflow-y-auto p-3 hidden">
                <div id="active-polls" class="space-y-3">
                    @if($currentState['active_polls'])
                        @foreach($currentState['active_polls'] as $poll)
                        <div class="bg-gray-700 rounded-lg p-4" data-poll-id="{{ $poll->id }}">
                            <p class="text-white font-medium mb-3">{{ $poll->question }}</p>
                            <div class="space-y-2" id="poll-options-{{ $poll->id }}">
                                @foreach($poll->options as $i => $opt)
                                <button class="poll-option w-full text-left bg-gray-600 hover:bg-gray-500 rounded px-3 py-2 text-sm text-gray-200 transition-colors" 
                                    data-poll-id="{{ $poll->id }}" 
                                    data-option="{{ $i }}">
                                    {{ chr(65+$i) }}. {{ $opt }}
                                </button>
                                @endforeach
                            </div>
                            <div id="poll-results-{{ $poll->id }}" class="hidden mt-3 space-y-2">
                                <!-- Results will be shown here after reveal -->
                            </div>
                        </div>
                        @endforeach
                    @else
                    <div class="text-center text-gray-500 py-8">
                        <p class="text-sm">No active polls</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Participants Tab -->
            <div id="tab-participants" class="tab-content flex-1 overflow-y-auto p-3 hidden">
                <div class="mb-3">
                    <div class="flex items-center gap-2 text-sm text-gray-400 mb-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        <span>Teacher</span>
                    </div>
                    <div class="bg-gray-700 rounded p-2 flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-sm">👨‍🏫</div>
                        <span class="text-white text-sm">{{ $session->teacher->name ?? 'Teacher' }}</span>
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-400 mb-2">Students ({{ $currentState['participant_count'] ?? 0 }})</div>
                    <div id="participants-list" class="space-y-1">
                        <!-- Will be populated -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Join Modal -->
    @if(!$participant)
    <div id="join-modal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h2 class="text-white text-xl font-bold mb-4">Join Class</h2>
            <div class="space-y-4">
                <div>
                    <label class="text-gray-400 text-sm block mb-1">Your Name</label>
                    <input type="text" id="join-name" 
                        value="{{ $student?->name ?? Auth::user()?->name ?? '' }}"
                        class="w-full bg-gray-700 text-white rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                @if($session->access_code)
                <div>
                    <label class="text-gray-400 text-sm block mb-1">Access Code</label>
                    <input type="text" id="join-code" 
                        class="w-full bg-gray-700 text-white rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter access code">
                </div>
                @endif
                <button id="btn-join" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-medium">
                    Join Class
                </button>
            </div>
        </div>
    </div>
    @endif

    <script>
        // Configuration
        const SESSION_ID = {{ $session->id }};
        const PARTICIPANT_ID = {{ $participant?->id ?? 'null' }};
        const STUDENT_NAME = '{{ $student?->name ?? Auth::user()?->name ?? "" }}';
        
        // State
        let participantId = PARTICIPANT_ID;
        let currentPdf = null;
        let pdfDoc = null;
        let isHandRaised = false;
        let lastTimestamp = 0;
        
        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            initTabs();
            initEventListeners();
            
            @if($participant)
            startPolling();
            initContent();
            @endif
        });

        function initTabs() {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const tab = btn.dataset.tab;
                    document.querySelectorAll('.tab-btn').forEach(b => {
                        b.classList.remove('border-b-2', 'border-blue-500', 'text-white', 'font-medium');
                        b.classList.add('text-gray-400');
                    });
                    btn.classList.add('border-b-2', 'border-blue-500', 'text-white', 'font-medium');
                    btn.classList.remove('text-gray-400');
                    
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
                    document.getElementById(`tab-${tab}`).classList.remove('hidden');
                });
            });
        }

        function initEventListeners() {
            // Join
            document.getElementById('btn-join')?.addEventListener('click', joinSession);
            
            // Chat
            document.getElementById('btn-send')?.addEventListener('click', sendChat);
            document.getElementById('chat-input')?.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') sendChat();
            });
            
            // Raise hand
            document.getElementById('btn-hand')?.addEventListener('click', toggleHand);
            
            // Leave
            document.getElementById('btn-leave')?.addEventListener('click', leaveSession);
            
            // Poll options
            document.querySelectorAll('.poll-option').forEach(btn => {
                btn.addEventListener('click', () => submitPollAnswer(btn.dataset.pollId, btn.dataset.option));
            });
        }

        async function joinSession() {
            const name = document.getElementById('join-name').value.trim();
            const code = document.getElementById('join-code')?.value || '';
            
            if (!name) {
                alert('Please enter your name');
                return;
            }
            
            try {
                const response = await fetch(`/btlive-v2/session/${SESSION_ID}/join`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ name, code }),
                });
                
                const data = await response.json();
                
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                participantId = data.participant_id;
                document.getElementById('join-modal').classList.add('hidden');
                
                // Initialize content
                initContent(data.state);
                startPolling();
                
            } catch (e) {
                console.error('Join error:', e);
                alert('Failed to join. Please try again.');
            }
        }

        function initContent(state = null) {
            const initialState = state || @json($currentState);
            
            // Load current PDF
            if (initialState.active_pdf) {
                loadPdf(initialState.active_pdf);
            }
        }

        function startPolling() {
            setInterval(pollState, 3000);
        }

        async function pollState() {
            if (!participantId) return;
            
            try {
                const response = await fetch(`/btlive-v2/participant/${participantId}/event`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        event_type: 'ping',
                        data: {},
                    }),
                });
                
                const data = await response.json();
                
                if (data.state) {
                    updateUI(data.state);
                }
                if (data.new_events) {
                    handleNewEvents(data.new_events);
                }
                
            } catch (e) {
                console.error('Poll error:', e);
            }
        }

        function updateUI(state) {
            // Update participant count
            document.querySelector('#tab-participants .text-gray-400').textContent = 
                `Students (${state.participant_count})`;
            
            // Update PDF page
            if (state.session.current_pdf_page) {
                document.getElementById('current-page').textContent = state.session.current_pdf_page;
                
                if (pdfDoc && state.session.current_pdf_page !== currentPage) {
                    currentPage = state.session.current_pdf_page;
                    renderPage(currentPage);
                }
            }
        }

        function handleNewEvents(events) {
            events.forEach(event => {
                switch (event.type) {
                    case 'pdf_page_change':
                        currentPage = event.page_number;
                        renderPage(currentPage);
                        break;
                    case 'annotation':
                        // Apply annotation to whiteboard
                        applyAnnotation(event);
                        break;
                    case 'poll_start':
                        showNewPoll(event);
                        break;
                    case 'poll_closed':
                        // Poll closed
                        break;
                    case 'poll_results_revealed':
                        showPollResults(event);
                        break;
                    case 'chat':
                        addChatMessage(event.message);
                        break;
                    case 'session_end':
                        alert('Class has ended');
                        window.location.href = '/';
                        break;
                }
            });
        }

        async function loadPdf(pdfData) {
            currentPdf = pdfData;
            
            try {
                const loadingTask = pdfjsLib.getDocument(pdfData.url);
                pdfDoc = await loadingTask.promise;
                
                document.getElementById('total-pages').textContent = pdfDoc.numPages;
                renderPage(1);
                
            } catch (e) {
                console.error('PDF load error:', e);
            }
        }

        function renderPage(pageNum) {
            if (!pdfDoc) return;
            
            pdfDoc.getPage(pageNum).then(page => {
                const canvas = document.getElementById('pdf-canvas');
                const ctx = canvas.getContext('2d');
                const viewport = page.getViewport({ scale: 1.2 });
                
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                page.render({ canvasContext: ctx, viewport });
                
                // Update whiteboard canvas
                const wbCanvas = document.getElementById('whiteboard-canvas');
                wbCanvas.width = viewport.width;
                wbCanvas.height = viewport.height;
                
                document.getElementById('current-page').textContent = pageNum;
            });
        }

        function applyAnnotation(event) {
            const canvas = document.getElementById('whiteboard-canvas');
            const ctx = canvas.getContext('2d');
            
            ctx.strokeStyle = event.config?.color || '#ff0000';
            ctx.lineWidth = event.config?.width || 3;
            ctx.lineCap = 'round';
            
            // Apply the annotation data
            if (event.data?.path) {
                ctx.beginPath();
                ctx.moveTo(event.data.path[0].x, event.data.path[0].y);
                for (let i = 1; i < event.data.path.length; i++) {
                    ctx.lineTo(event.data.path[i].x, event.data.path[i].y);
                }
                ctx.stroke();
            }
        }

        function showNewPoll(event) {
            const pollDiv = document.createElement('div');
            pollDiv.className = 'bg-gray-700 rounded-lg p-4';
            pollDiv.dataset.pollId = event.poll.id;
            pollDiv.innerHTML = `
                <p class="text-white font-medium mb-3">${event.poll.question}</p>
                <div class="space-y-2" id="poll-options-${event.poll.id}">
                    ${event.poll.options.map((opt, i) => `
                        <button class="poll-option w-full text-left bg-gray-600 hover:bg-gray-500 rounded px-3 py-2 text-sm text-gray-200 transition-colors" 
                            data-poll-id="${event.poll.id}" 
                            data-option="${i}">
                            ${String.fromCharCode(65+i)}. ${opt}
                        </button>
                    `).join('')}
                </div>
            `;
            document.getElementById('active-polls').appendChild(pollDiv);
            
            // Add listeners
            pollDiv.querySelectorAll('.poll-option').forEach(btn => {
                btn.addEventListener('click', () => submitPollAnswer(btn.dataset.pollId, btn.dataset.option));
            });
        }

        function showPollResults(event) {
            const container = document.getElementById(`poll-results-${event.poll_id}`);
            if (!container) return;
            
            container.classList.remove('hidden');
            
            const results = event.results;
            container.innerHTML = results.options.map(opt => `
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-gray-600 rounded-full h-4 relative overflow-hidden">
                        <div class="bg-blue-500 h-full" style="width: ${opt.percentage}%"></div>
                    </div>
                    <span class="text-white text-sm w-12 text-right">${opt.percentage}%</span>
                </div>
                <div class="text-gray-400 text-xs ml-2">${opt.text} (${opt.count} votes)</div>
            `).join('');
        }

        function addChatMessage(message) {
            const container = document.getElementById('chat-messages');
            const div = document.createElement('div');
            div.className = message.type === 'teacher' ? 'bg-blue-900/50 rounded p-2' : 'bg-gray-700 rounded p-2';
            div.innerHTML = `
                <div class="text-xs ${message.type === 'teacher' ? 'text-blue-300' : 'text-gray-400'}">${message.name}</div>
                <div class="text-gray-200 text-sm">${message.content}</div>
            `;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
        }

        async function sendChat() {
            const input = document.getElementById('chat-input');
            const content = input.value.trim();
            if (!content || !participantId) return;
            
            input.value = '';
            
            await fetch(`/btlive-v2/participant/${participantId}/event`, {
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

        async function toggleHand() {
            if (!participantId) return;
            
            const btn = document.getElementById('btn-hand');
            
            if (isHandRaised) {
                // Lower hand
                await fetch(`/btlive-v2/participant/${participantId}/event`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        event_type: 'lower_hand',
                        data: {},
                    }),
                });
                
                btn.classList.remove('bg-yellow-500');
                btn.classList.add('bg-yellow-600');
                btn.innerHTML = '<span>✋</span><span>Raise Hand</span>';
                isHandRaised = false;
                
            } else {
                // Raise hand
                await fetch(`/btlive-v2/participant/${participantId}/event`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        event_type: 'raise_hand',
                        data: {},
                    }),
                });
                
                btn.classList.remove('bg-yellow-600');
                btn.classList.add('bg-yellow-500');
                btn.innerHTML = '<span>✋</span><span>Hand Raised</span>';
                isHandRaised = true;
            }
        }

        async function submitPollAnswer(pollId, optionIndex) {
            if (!participantId) return;
            
            await fetch(`/btlive-v2/participant/${participantId}/event`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    event_type: 'poll_answer',
                    data: { poll_id: pollId, option_index: parseInt(optionIndex) },
                }),
            });
            
            // Disable poll options
            document.querySelectorAll(`[data-poll-id="${pollId}"].poll-option`).forEach(btn => {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            });
        }

        async function leaveSession() {
            if (!confirm('Are you sure you want to leave the class?')) return;
            
            if (participantId) {
                await fetch(`/btlive-v2/participant/${participantId}/event`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        event_type: 'leave',
                        data: {},
                    }),
                });
            }
            
            window.location.href = '/';
        }

        // Handle page unload
        window.addEventListener('beforeunload', () => {
            if (participantId) {
                navigator.sendBeacon(`/btlive-v2/participant/${participantId}/event`,
                    JSON.stringify({
                        event_type: 'leave',
                        data: {},
                    }));
            }
        });
    </script>
</body>
</html>
