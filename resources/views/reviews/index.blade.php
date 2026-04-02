@extends('layouts.app')

@section('title', 'Trainer Reviews')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Trainer Reviews</h1>
            <p class="section-subtitle">Manage trainer reviews and ratings</p>
        </div>
        <a href="{{ route('reviews.create') }}" class="btn btn-primary">+ New Review</a>
    </div>

    @if ($reviews->count())
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Trainer</th>
                        <th>Member</th>
                        <th>Rating</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reviews as $review)
                        <tr>
                            <td>#{{ $review->id }}</td>
                            <td>{{ $review->trainer->user->name ?? 'N/A' }}</td>
                            <td>{{ $review->member->user->name ?? 'N/A' }}</td>
                            <td><span style="color: #ffd700;">★ {{ $review->rating }}/5</span></td>
                            <td>{{ $review->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('reviews.show', $review) }}" class="btn btn-secondary">View</a>
                                    <a href="{{ route('reviews.edit', $review) }}" class="btn btn-secondary">Edit</a>
                                    <form action="{{ route('reviews.destroy', $review) }}" method="POST" style="display:inline;">
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
            No reviews found. <a href="{{ route('reviews.create') }}" style="color: #ffd700; font-weight: bold;">Create one now</a>
        </div>
    @endif
@endsection
