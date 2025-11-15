@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')

<body class="bg-green-50 min-h-screen">
    <div class="grid grid-cols-2 gap-4 h-screen p-6">

        <div class="flex flex-col gap-4">
            <div class="bg-red-700 p-6 flex-1">Membership Overview</div>
            <div class="bg-red-600 p-6 flex-1">Inventory Overview</div>
            <div class="bg-red-500 p-6 flex-1">Gym Membership Trend</div>
            <div class="bg-red-400 p-6 flex-1">Transaction Chart</div>
        </div>


        <div class="grid grid-rows-4 gap-4">
            <div class="bg-blue-700 p-6">Gym Occupancy</div>
            <div class="bg-blue-600 p-6">Stock in Stock out</div>
            <div class="bg-blue-500 p-6">Sales</div>
            <div class="bg-blue-400 p-6">Low and High Selling Product</div>
        </div>

    </div>
</body>

@endsection
