@extends('layouts.app')

@section('title', 'Trainer Applications')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="section-title">Trainer Applications</h1>
            <p class="section-subtitle">Manage trainer application requests</p>
        </div>
    </div>

    @if ($applications->count())
        <div class="card">
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Applicant</th>
                            <th>Status</th>
                            <th>Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $app)
                            <tr>
                                <td>#{{ $app->id }}</td>
                                <td>{{ $app->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span style="font-weight:600; color:
                                        {{ $app->status === 'approved' ? '#5fd68f' :
                                           ($app->status === 'rejected' ? '#ff5555' : '#ffd700') }};">
                                        {{ ucfirst($app->status) }}
                                    </span>
                                </td>
                                <td>{{ $app->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('trainer-applications.show', $app) }}" class="btn btn-secondary">
                                            View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card" style="text-align:center; padding:40px;">
            <h3 style="color:#ffd700;">No Applications Found</h3>
            <p style="color:#aaa;">There are no trainer applications at the moment.</p>
        </div>
    @endif

@endsection