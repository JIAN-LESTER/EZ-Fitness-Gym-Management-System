@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')

<body class="bg-gray-100 min-h-screen flex flex-col items-center p-8">

    <div class="w-full max-w-6xl bg-white rounded-xl shadow-md p-6">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Product List</h1>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Category</th>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Description</th>
                        <th class="px-4 py-2 text-left">Price</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($products as $product)
                        <tr class="hover:bg-gray-100 transition">
                            <td class="px-4 py-2 text-gray-700">{{ $product->product_id }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $product->category->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 font-medium text-gray-800">{{ $product->name }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $product->description }}</td>
                            <td class="px-4 py-2 text-gray-700">₱{{ number_format($product->price, 2) }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded-full text-sm 
                                    {{ $product->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>

@endsection