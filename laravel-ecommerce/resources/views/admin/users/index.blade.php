<x-app-layout>
    <div class="container py-5">

        <h2 class="fw-bold text-center text-primary mb-5">
            <i class="bi bi-people-fill me-2"></i> Manage Users
        </h2>

        {{-- Success Message --}}
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show text-center mx-auto w-75" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4 justify-content-center">
            @forelse($users as $user)
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-lg rounded-4 user-card h-100">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:50px; height:50px; font-size:20px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">{{ $user->name }}</h5>
                                    <p class="text-muted mb-0 small">{{ $user->email }}</p>
                                </div>
                            </div>

                            <div class="mt-3">
                                <p class="mb-1"><strong>Role:</strong> 
                                    <span class="badge {{ $user->role === 'admin' ? 'bg-dark' : 'bg-info text-dark' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </p>

                                <form method="POST" 
                                      action="{{ route('admin.users.changeRole', $user->id) }}" 
                                      onsubmit="return confirm('Change role for {{ $user->name }}?')">
                                    @csrf
                                    <select name="role" class="form-select form-select-sm mt-2" onchange="this.form.submit()">
                                        <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Customer</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </form>
                            </div>

                            <div class="mt-3">
                                <p class="mb-1"><strong>Status:</strong> 
                                    <span class="badge {{ $user->is_blocked ? 'bg-danger' : 'bg-success' }}">
                                        {{ $user->is_blocked ? 'Blocked' : 'Active' }}
                                    </span>
                                </p>

                                @php
                                    $actionText = $user->is_blocked ? 'unblock' : 'block';
                                @endphp
                                <form method="POST" 
                                    action="{{ route('admin.users.toggle-block', $user->id) }}" 
                                    onsubmit="return confirm('Are you sure you want to {{ $actionText }} this user?')">
                                    @csrf
                                    <button type="submit" class="btn w-100 mt-2 {{ $user->is_blocked ? 'btn-outline-success' : 'btn-outline-danger' }}">
                                        <i class="bi {{ $user->is_blocked ? 'bi-unlock-fill' : 'bi-lock-fill' }} me-1"></i>
                                        {{ $user->is_blocked ? 'Unblock User' : 'Block User' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-emoji-frown fs-1"></i>
                    <p class="mt-2">No users found.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f9fbfd;
        }
        .user-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .user-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .avatar {
            font-weight: 600;
        }
    </style>
</x-app-layout>
