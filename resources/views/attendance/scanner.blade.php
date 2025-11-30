@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">QR Code Scanner</h1>
            <p class="text-gray-600">Scan member QR codes for attendance check-in</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Camera Scanner</h3>
                    <div class="relative">
                        <div id="qr-reader" style="width: 100%;"></div>
                        <div id="scanner-status" class="mt-3 text-center text-sm text-gray-600">
                            Initializing camera...
                        </div>
                    </div>
                    
                    <!-- Control sa Camera -->
                    <div class="mt-4 flex gap-2">
                        <button id="start-scan" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            Start Scanning
                        </button>
                        <button id="stop-scan" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition" disabled>
                            Stop Scanning
                        </button>
                    </div>
                </div>

                <!-- Ang user profile -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Scan Result</h3>
                    <div id="result-container" class="bg-gray-50 rounded-lg p-6 min-h-[300px]">
                        <div class="text-center text-gray-400">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                            <p>Scan a QR code to see member details</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent nga Check-ins -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-semibold mb-4">Recent Check-ins Today</h3>
            <div id="recent-checkins">
                <p class="text-gray-500 text-center py-4">No check-ins yet today</p>
            </div>
        </div>
    </div>
</div>

<!-- QR Scanner Library gamit JS -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrCode;
let isScanning = false;

document.addEventListener('DOMContentLoaded', function() {
    const startBtn = document.getElementById('start-scan');
    const stopBtn = document.getElementById('stop-scan');
    const statusDiv = document.getElementById('scanner-status');
    const resultContainer = document.getElementById('result-container');

    // Initialize sa scanner
    html5QrCode = new Html5Qrcode("qr-reader");

    // Start scanning
    startBtn.addEventListener('click', async function() {
        try {
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };

            await html5QrCode.start(
                { facingMode: "environment" },
                config,
                onScanSuccess,
                onScanFailure
            );
            
            isScanning = true;
            startBtn.disabled = true;
            stopBtn.disabled = false;
            statusDiv.textContent = 'Scanning... Point camera at QR code';
            statusDiv.className = 'mt-3 text-center text-sm text-green-600 font-semibold';
        } catch (err) {
            console.error('Scanner error:', err);
            statusDiv.textContent = 'Error: ' + err;
            statusDiv.className = 'mt-3 text-center text-sm text-red-600';
        }
    });

    // Stop scanning
    stopBtn.addEventListener('click', async function() {
        if (isScanning) {
            try {
                await html5QrCode.stop();
                isScanning = false;
                startBtn.disabled = false;
                stopBtn.disabled = true;
                statusDiv.textContent = 'Scanner stopped';
                statusDiv.className = 'mt-3 text-center text-sm text-gray-600';
            } catch (err) {
                console.error('Stop error:', err);
            }
        }
    });

    // Handle successful scan
    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanning temporarily to process
        html5QrCode.pause();
        
        statusDiv.textContent = 'Processing...';
        statusDiv.className = 'mt-3 text-center text-sm text-yellow-600 font-semibold';
        
        // Send to server
        fetch('{{ route('attendance.scan') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                qr_data: decodedText
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data);
            } else {
                showError(data.message);
            }
            
            // Resume scanning after 3 seconds
            setTimeout(() => {
                if (isScanning) {
                    html5QrCode.resume();
                    statusDiv.textContent = 'Scanning... Point camera at QR code';
                    statusDiv.className = 'mt-3 text-center text-sm text-green-600 font-semibold';
                }
            }, 3000);
        })
        .catch(error => {
            showError('Network error: ' + error.message);
            setTimeout(() => {
                if (isScanning) {
                    html5QrCode.resume();
                    statusDiv.textContent = 'Scanning... Point camera at QR code';
                    statusDiv.className = 'mt-3 text-center text-sm text-green-600 font-semibold';
                }
            }, 3000);
        });
    }

    function onScanFailure(error) {
        // Ignore scan failures (happens continuously while scanning)
    }

    // Display success result
    function showSuccess(data) {
        resultContainer.innerHTML = `
            <div class="text-center">
                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h4 class="text-2xl font-bold text-green-600 mb-2">Check-in Successful!</h4>
                <div class="bg-white rounded-lg p-4 mt-4 text-left">
                    <p class="text-gray-700 mb-2"><strong>Name:</strong> ${data.member.name}</p>
                    <p class="text-gray-700 mb-2"><strong>Email:</strong> ${data.member.email}</p>
                    <p class="text-gray-700 mb-2"><strong>Plan:</strong> ${data.member.plan}</p>
                    <p class="text-gray-700"><strong>Time:</strong> ${data.member.check_in_time}</p>
                </div>
            </div>
        `;
        
        // Play success sound (optional - browser beep)
        playBeep();
    }

    // Display error result
    function showError(message) {
        resultContainer.innerHTML = `
            <div class="text-center">
                <div class="w-16 h-16 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h4 class="text-2xl font-bold text-red-600 mb-2">Check-in Failed</h4>
                <p class="text-gray-700 mt-4">${message}</p>
            </div>
        `;
    }

    // Simple beep sound
    function playBeep() {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.5);
    }

    // Auto-start scanner on page load
    setTimeout(() => {
        startBtn.click();
    }, 500);
});
</script>

<style>
#qr-reader {
    border: 4px solid #3b82f6;
    border-radius: 8px;
    overflow: hidden;
}

#qr-reader video {
    width: 100% !important;
    height: auto !important;
    display: block;
}

#qr-reader__dashboard {
    display: none !important;
}

#qr-reader__scan_region {
    border: 2px solid #3b82f6 !important;
}
</style>
@endsection