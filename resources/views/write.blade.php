@extends('layout')

@section('content')
    
    <section class="write-section">
        <p class="section-title">Write question and answer</p>
        <form action="{{ route('documents.store') }}" method="POST" class="write-form">
            @csrf
            <div class="form-group language form-radio-group">
                <div class="form-radio">
                    <input type="radio" id="en" name="lang" value="en" checked>
                    <label for="en">EN</label>
                </div>
                <div class="form-radio">
                    <input type="radio" id="ar" name="lang" value="ar">
                    <label for="ar">AR</label>
                </div>
            </div>
            <div class="form-group">
                <label for="question">Question</label>
                <textarea name="question" id="question" cols="30" rows="2" required></textarea>
            </div>
            <div class="form-group">
                <label for="answer">Answer</label>
                <textarea name="answer" id="answer" cols="30" rows="5" required></textarea>
            </div>
            <div class="form-group options">
                <button type="submit" class="btn-primary">Add question and answer</button>
            </div>
        </form>
    </section>
@endsection
