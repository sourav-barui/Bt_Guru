<!DOCTYPE html>
<html>
<head>
    <title>BTLive - {{ $liveClass->title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; }
        #meet { width: 100%; height: 100vh; }
        .header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 { font-size: 1.2rem; }
        .header button {
            background: white;
            color: #764ba2;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>{{ $liveClass->title }}</h1>
            <small>{{ $liveClass->course?->title ?? 'Live Class' }}</small>
        </div>
        <button onclick="leaveClass()">Leave Class</button>
    </div>
    <div id="meet"></div>

    <script src='https://meet.jit.si/external_api.js'></script>
    <script>
        const domain = '{{ $jitsiConfig['domain'] }}';
        const roomName = '{{ $jitsiConfig['roomName'] }}';
        
        const api = new JitsiMeetExternalAPI(domain, {
            roomName: roomName,
            parentNode: document.getElementById('meet'),
            width: '100%',
            height: '100%'
        });

        function leaveClass() {
            api.executeCommand('hangup');
            window.location.href = '{{ route('student.live_classes.index') }}';
        }
    </script>
</body>
</html>
