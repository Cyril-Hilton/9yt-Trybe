@extends('layouts.admin')

@section('title', 'Shop Product Management')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">Shop Product Management</h1>
                <p class="text-gray-400 text-sm sm:text-base">Review and approve shop products from vendors</p>
            </div>
            <a href="{{ route('admin.shop.create') }}" class="px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition shadow-lg flex items-center justify-center text-sm sm:text-base whitespace-nowrap">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Product
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        @php
            $total = \App\Models\ShopProduct::count();
            $pending = \App\Models\ShopProduct::where('status', 'pending')->count();
            $approved = \App\Models\ShopProduct::where('status', 'approved')->count();
            $rejected = \App\Models\ShopProduct::where('status', 'rejected')->count();
        @endphp
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-gray-700">
            <p class="text-gray-400 text-sm mb-1">Total Products</p>
            <p class="text-3xl font-bold text-white">{{ $total }}</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-600 to-yellow-700 rounded-xl p-6">
            <p class="text-yellow-100 text-sm mb-1">Pending Approval</p>
            <p class="text-3xl font-bold text-white">{{ $pending }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6">
            <p class="text-green-100 text-sm mb-1">Approved</p>
            <p class="text-3xl font-bold text-white">{{ $approved }}</p>
        </div>
        <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-xl p-6">
            <p class="text-red-100 text-sm mb-1">Rejected</p>
            <p class="text-3xl font-bold text-white">{{ $rejected }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800 rounded-xl p-4 mb-6 border border-gray-700">
        <form action="{{ route('admin.shop.index') }}" method="GET" class="flex flex-wrap gap-4">
            <select name="status" class="px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>

            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Filter
            </button>

            @if(request()->has('status') && request('status') !== 'all')
            <a href="{{ route('admin.shop.index') }}" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-gray-800 rounded-xl overflow-x-auto border border-gray-700">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Active</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Submitted</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($products as $product)
                <tr class="hover:bg-gray-750 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-lg object-cover mr-3">
                            @else
                            <div class="w-12 h-12 rounded-lg bg-gray-700 mr-3 flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-white">{{ Str::limit($product->name, 40) }}</div>
                                <div class="text-xs text-gray-400">{{ Str::limit($product->description, 50) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-white font-semibold">GH₵{{ number_format($product->price, 2) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                        {{ $product->stock }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($product->status === 'approved') bg-green-900 text-green-200
                            @elseif($product->status === 'pending') bg-yellow-900 text-yellow-200
                            @elseif($product->status === 'rejected') bg-red-900 text-red-200
                            @endif">
                            {{ ucfirst($product->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($product->status === 'approved')
                        <form action="{{ route('admin.shop.toggle-active', $product) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->is_active ? 'bg-blue-900 text-blue-200' : 'bg-gray-700 text-gray-300' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                        @else
                        <span class="text-xs text-gray-500">N/A</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">
                        {{ $product->created_at->format('M j, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('admin.shop.edit', $product) }}"
                               class="text-indigo-400 hover:text-indigo-300">Edit</a>

                            @if($product->status === 'pending')
                            <button onclick="approveProduct({{ $product->id }})"
                                    class="text-green-400 hover:text-green-300">Approve</button>

                            <button onclick="openRejectModal({{ $product->id }}, {{ json_encode($product->name) }})"
                                    class="text-yellow-400 hover:text-yellow-300">Reject</button>
                            @endif

                            @if($product->status === 'rejected' && $product->rejection_reason)
                            <button onclick="showRejectionReason({{ json_encode($product->rejection_reason) }})"
                                    class="text-gray-400 hover:text-gray-300">View Reason</button>
                            @endif

                            <button onclick="deleteProduct({{ $product->id }}, {{ json_encode($product->name) }})"
                                    class="text-red-400 hover:text-red-300">Delete</button>
                        </div>

                        <!-- Hidden forms -->
                        <form id="approve-form-{{ $product->id }}" action="{{ route('admin.shop.approve', $product) }}" method="POST" style="display: none;">
                            @csrf
                        </form>

                        <form id="delete-form-{{ $product->id }}" action="{{ route('admin.shop.destroy', $product) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <p class="text-gray-400">No products found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($products->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-gray-800 rounded-xl p-6 max-w-md w-full mx-4 border border-gray-700">
        <h3 class="text-xl font-bold text-white mb-4">Reject Product</h3>
        <p class="text-gray-400 mb-4">Are you sure you want to reject <span id="productName" class="font-semibold text-white"></span>?</p>

        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-2">Rejection Reason *</label>
                <textarea name="rejection_reason" required rows="3"
                          class="w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                          placeholder="Please provide a reason for rejection..."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Reject Product
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Rejection Reason Modal -->
<div id="reasonModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-gray-800 rounded-xl p-6 max-w-md w-full mx-4 border border-gray-700">
        <h3 class="text-xl font-bold text-white mb-4">Rejection Reason</h3>
        <p class="text-gray-300 mb-4" id="rejectionReasonText"></p>
        <div class="flex justify-end">
            <button onclick="closeReasonModal()"
                    class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function approveProduct(id) {
    if (confirm('Are you sure you want to approve this product?')) {
        document.getElementById('approve-form-' + id).submit();
    }
}

function openRejectModal(id, name) {
    document.getElementById('productName').textContent = name;
    document.getElementById('rejectForm').action = '/admin/shop/' + id + '/reject';
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}

function deleteProduct(id, name) {
    if (confirm('Are you sure you want to delete "' + name + '"? This action cannot be undone.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}

function showRejectionReason(reason) {
    document.getElementById('rejectionReasonText').textContent = reason;
    document.getElementById('reasonModal').style.display = 'flex';
}

function closeReasonModal() {
    document.getElementById('reasonModal').style.display = 'none';
}

// Close modals when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});

document.getElementById('reasonModal').addEventListener('click', function(e) {
    if (e.target === this) closeReasonModal();
});
</script>
@endsection
