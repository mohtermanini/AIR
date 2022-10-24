<nav class="main-navbar">
    <div class='title'>
        <h1>AIR Search Engine</h1>
    </div>
    <div class="options">
        <ul>
            <li>
                <a href="{{route('home')}}" class="linear-primary {{$page_active == "home"? "active":"deactive"}}">
                    Home
                </a>
            </li>
            <li>
                <a href="{{route('documents.create')}}" class="linear-primary {{$page_active == "write"? "active":"deactive"}}">
                    Enter Q&A
                </a>
            </li>
        </ul>
    </div>

</nav>
