@if ($errors->any())
     @foreach ($errors->all() as $error)
         <div class="alert alert-danger">{{$error}}</div>
     @endforeach
 @endif

 @if(Session::has("success"))
 <div class="alert alert-success">
     {{Session::get("success")}}
 </div>
@endif