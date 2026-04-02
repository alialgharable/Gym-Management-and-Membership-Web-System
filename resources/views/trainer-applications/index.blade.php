@extends('layouts.app')

@section('title', 'Trainer Applications')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Trainer Applications</h1>
            <p class="section-subtitle">Manage trainer application requests</p>
        </div>
        <a href="{{ route('trainer-applications.create') }}" class="btn btn-primary">+ New Application</a>
    </div>

    @if ($applications->count())
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Applicant</th>
                        <th>Status</th>
                        <th>Applied On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($applications as $app)
                        <tr>
                            <td>#{{ $app->id }}</td>
                            <td>{{ $app->user->name ?? 'N/A' }}</td>
                            <td>
                                <span style="color: {{ $app->status === 'approved' ? '#5fd68f' : ($app->status === 'rejected' ? '#ff5555' : '#ffd700') }};">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </td>
                            <td>{{ $app->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('trainer-applications.show', $app) }}" class="btn btn-secondary">View</a>
                                    <a href="{{ route('trainer-applications.edit', $app) }}" class="btn btn-secondary">Edit</a>
                                    <form action="{{ route('trainer-applications.destroy', $app) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-warning">
            No applications found.
        </div>
    @endif
@endsection
