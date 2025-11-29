@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')



    <body class="bg-green-50 flex items-center justify-center min-h-screen">
        <div class="bg-white shadow-lg rounded-2xl p-8 text-center max-w-md w-full">
            <h1 class="text-2xl font-bold text-green-700 mb-2">This is the Member Dashboard</h1>
            <p class="text-gray-600">Welcome to your dashboard area. More features coming soon!</p>
        </div>
    </body>


    @if(session('show_qr_popup'))
        <div id="qrModal" class="fixed inset-0 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <h2 class="text-xl text-gray-800 font-semibold mb-4">🎉 Membership QR Code</h2>
                <img src="{{ session('qr_path') }}" alt="QR Code" class="mx-auto mb-4 w-48 h-48">
                <p class="text-gray-800">Your QR code has also been emailed to you at <b>{{ Auth::user()->email }}</b>.</p>
                <button onclick="document.getElementById('qrModal').remove()"
                    class="mt-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Close
                </button>
            </div>
        </div>
    @endif

@endsection