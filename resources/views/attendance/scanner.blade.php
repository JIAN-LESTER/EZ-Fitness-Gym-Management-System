@extends('layouts.app')
@section('title', 'Attendance QR')
@section('header', 'Attendance QR')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="max-w-[1800px] mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">QR Code Scanner</h1>
                    <p class="text-gray-600">Scan member QR codes for attendance check-in/check-out</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Today's Date</p>
                    <p class="text-lg font-semibold text-gray-800">{{ now()->format('F d, Y') }}</p>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-12 gap-6">
            <!-- Scanner Container (Left Side - 4 columns) -->
            <div class="lg:col-span-4">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Camera Scanner</h3>
                    
                    <!-- QR Reader Container -->
                    <div class="relative mb-4">
                        <div id="qr-reader" style="width: 100%;"></div>
                        <div id="scanner-status" class="mt-3 text-center text-sm text-gray-600 font-medium">
                            Initializing camera...
                        </div>
                    </div>
                    
                    <!-- Camera Controls -->
                    <div class="flex gap-3 mb-6">
                        <button id="start-scan" class="flex-1 bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                            Start Scanning
                        </button>
                        <button id="stop-scan" class="flex-1 bg-red-600 text-white px-4 py-3 rounded-lg hover:bg-red-700 transition font-medium" disabled>
                            Stop Scanning
                        </button>
                    </div>

                    <!-- Result Display -->
                    <div class="border-t pt-4">
                        <h4 class="text-lg font-semibold mb-3 text-gray-800">Scan Result</h4>
                        <div id="result-container" class="bg-gray-50 rounded-lg p-6 min-h-[280px]">
                            <div class="text-center text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                </svg>
                                <p class="text-sm">Scan a QR code to see member details</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Table (Right Side - 8 columns) -->
            <div class="lg:col-span-8">
                <div class="bg-white rounded-lg shadow-sm">
                    <!-- Table Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800">Today's Attendance</h3>
                                <p class="text-sm text-gray-600 mt-1">Real-time check-in and check-out logs</p>
                            </div>
                            <div class="flex gap-3">
                                <div class="text-center bg-green-50 px-4 py-2 rounded-lg">
                                    <p class="text-xs text-green-600 font-medium">Checked In</p>
                                    <p id="checkin-count" class="text-2xl font-bold text-green-700">0</p>
                                </div>
                                <div class="text-center bg-blue-50 px-4 py-2 rounded-lg">
                                    <p class="text-xs text-blue-600 font-medium">Checked Out</p>
                                    <p id="checkout-count" class="text-2xl font-bold text-blue-700">0</p>
                                </div>
                                <div class="text-center bg-gray-50 px-4 py-2 rounded-lg">
                                    <p class="text-xs text-gray-600 font-medium">Total</p>
                                    <p id="total-count" class="text-2xl font-bold text-gray-700">0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Content -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">
                                        Member
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Plan
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Check-in
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Check-out
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Duration
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="attendance-table-body" class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <p>No attendance records yet today</p>
                                        <p class="text-sm text-gray-400 mt-1">Scan QR codes to start logging attendance</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Table is scrollable if many records -->
                    <div class="h-[600px] overflow-y-auto" id="table-scroll-container">
                        <!-- Table will be placed here via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include QR Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrCode;
let isScanning = false;

document.addEventListener('DOMContentLoaded', function() {
    const startBtn = document.getElementById('start-scan');
    const stopBtn = document.getElementById('stop-scan');
    const statusDiv = document.getElementById('scanner-status');
    const resultContainer = document.getElementById('result-container');

    // Initialize scanner
    html5QrCode = new Html5Qrcode("qr-reader");

    // Load initial attendance data
    loadTodayAttendance();

    // Refresh attendance list every 10 seconds
    setInterval(loadTodayAttendance, 10000);

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
                if (data.action === 'checkin') {
                    showCheckInSuccess(data);
                } else {
                    showCheckOutSuccess(data);
                }
                // Refresh attendance list
                loadTodayAttendance();
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

    // Display check-in success
    function showCheckInSuccess(data) {
        resultContainer.innerHTML = `
            <div class="text-center">
                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <h4 class="text-2xl font-bold text-green-600 mb-2">✓ Checked In</h4>
                <div class="bg-white rounded-lg p-4 mt-4 text-left border border-green-200">
                    <p class="text-gray-700 mb-2"><strong>Name:</strong> ${data.member.name}</p>
                    <p class="text-gray-700 mb-2"><strong>Plan:</strong> ${data.member.plan}</p>
                    <p class="text-gray-700"><strong>Time:</strong> ${data.member.check_in_time}</p>
                </div>
            </div>
        `;
        playBeep();
    }

    // Display check-out success
    function showCheckOutSuccess(data) {
        resultContainer.innerHTML = `
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <h4 class="text-2xl font-bold text-blue-600 mb-2">✓ Checked Out</h4>
                <div class="bg-white rounded-lg p-4 mt-4 text-left border border-blue-200">
                    <p class="text-gray-700 mb-2"><strong>Name:</strong> ${data.member.name}</p>
                    <p class="text-gray-700 mb-2"><strong>Plan:</strong> ${data.member.plan}</p>
                    <p class="text-gray-700 mb-2"><strong>Check-in:</strong> ${data.member.check_in_time}</p>
                    <p class="text-gray-700 mb-2"><strong>Check-out:</strong> ${data.member.check_out_time}</p>
                    <p class="text-gray-700"><strong>Duration:</strong> ${data.member.duration}</p>
                </div>
            </div>
        `;
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
                <h4 class="text-2xl font-bold text-red-600 mb-2">Failed</h4>
                <p class="text-gray-700 mt-4 text-sm">${message}</p>
            </div>
        `;
    }

    // Load today's attendance list
    function loadTodayAttendance() {
        fetch('{{ route('attendance.today') }}')
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('attendance-table-body');
                const totalCount = document.getElementById('total-count');
                const checkinCount = document.getElementById('checkin-count');
                const checkoutCount = document.getElementById('checkout-count');
                
                if (data.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p>No attendance records yet today</p>
                                <p class="text-sm text-gray-400 mt-1">Scan QR codes to start logging attendance</p>
                            </td>
                        </tr>
                    `;
                    totalCount.textContent = '0';
                    checkinCount.textContent = '0';
                    checkoutCount.textContent = '0';
                    return;
                }

                // Calculate counts
                const checkedInCount = data.filter(a => a.status === 'checked_in').length;
                const checkedOutCount = data.filter(a => a.status === 'checked_out').length;
                
                totalCount.textContent = data.length;
                checkinCount.textContent = checkedInCount;
                checkoutCount.textContent = checkedOutCount;
                
                tableBody.innerHTML = data.map((attendance, index) => {
                    const isCheckedOut = attendance.status === 'checked_out';
                    const statusColor = isCheckedOut ? 'blue' : 'green';
                    const statusText = isCheckedOut ? 'Checked Out' : 'Checked In';
                    const rowBg = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
                    
                    return `
                        <tr class="${rowBg} hover:bg-gray-100 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-${statusColor}-500 to-${statusColor}-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        ${attendance.name.split(' ').map(n => n[0]).join('').substring(0, 2)}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">${attendance.name}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    ${attendance.plan}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center text-sm text-gray-900">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"></path>
                                    </svg>
                                    ${attendance.check_in_time}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                ${attendance.check_out_time ? `
                                    <div class="flex items-center text-sm text-gray-900">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                        </svg>
                                        ${attendance.check_out_time}
                                    </div>
                                ` : `
                                    <span class="text-sm text-gray-400">—</span>
                                `}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                ${attendance.duration ? `
                                    <span class="text-sm font-medium text-gray-700">${attendance.duration}</span>
                                ` : `
                                    <span class="text-sm text-gray-400">In progress</span>
                                `}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-${statusColor}-100 text-${statusColor}-800">
                                    ${statusText}
                                </span>
                            </td>
                        </tr>
                    `;
                }).join('');
            })
            .catch(error => {
                console.error('Error loading attendance:', error);
            });
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
    border-radius: 12px;
    overflow: hidden;
}

#qr-reader video {
    width: 100% !important;
    height: auto !important;
    display: block;
    transform: scaleX(-1) !important; /* Prevent mirroring */

}

#qr-reader__dashboard {
    display: none !important;
}

#qr-reader__scan_region {
    border: 2px solid #3b82f6 !important;
}

/* Custom scrollbar */
#table-scroll-container::-webkit-scrollbar {
    width: 8px;
}

#table-scroll-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

#table-scroll-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

#table-scroll-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Smooth animations */
tr {
    transition: background-color 0.2s ease;
}
</style>
@endsection