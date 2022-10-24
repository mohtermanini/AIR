@extends('layout')

@section('content')
    <section class="search-options">
        <p class="section-title">Choose your options</p>
        <form action="{{ route('documents.search') }}" method="GET" class="search-form">
            <div class="language form-radio-group">
                <div class="form-radio">
                    <input type="radio" id="en" name="lang" value="en" 
                        {{$lang=="en"? "checked": ""}}
                    >
                    <label for="en">EN</label>
                </div>
                <div class="form-radio">
                    <input type="radio" id="ar" name="lang" value="ar"
                        {{$lang=="ar"? "checked": ""}}
                    >
                    <label for="ar">AR</label>
                </div>
            </div>
            <div class="algorithm form-radio-group">
                <div class="form-radio">
                    <input type="radio" id="boolean-model" name="algorithm" value="boolean-model" 
                        {{$algorithm=="boolean-model"? "checked": ""}}
                    >
                    <label for="boolean-model">Boolean Model</label>
                </div>
                <div class="form-radio">
                    <input type="radio" id="extended-boolean-model" name="algorithm" value="extended-boolean-model"
                    {{$algorithm=="extended-boolean-model"? "checked": ""}}
                    >
                    <label for="extended-boolean-model">Extended Boolean Model</label>
                </div>
                <div class="form-radio">
                    <input type="radio" id="vector-modal" name="algorithm" value="vector-modal"
                    {{$algorithm=="vector-modal"? "checked": ""}}>
                    <label for="vector-modal">Vector Model</label>
                </div>
            </div>
            <div class="form-group search-boxes">
                <div class="search-box">
                    <input type="text" placeholder="Search for question..." name="queries[]" autocomplete="off" required>
                </div>
            </div>
            <div class="options">
                <button class="btn-secondary btn-add-or-expression" type="button">Add OR Expression</button>
                <button class="btn-secondary btn-add-not-expression" type="button">Exclude Words</button>
                <button class="btn-primary" type="submit">Search</button>
            </div>
        </form>
    </section>

    @if (isset($results))
        <section class="search-results">
            <div class="results-bar">
                <h2>Search Results using
                    <span class="txt-marked">
                        {{
                         (
                            $algorithm == "boolean-model" ? "Boolean Model" : 
                            ($algorithm == "extended-boolean-model" ? "Extended Boolean Model": "Vector Model") 
                         )
                        }}</span>
                    in
                    <span class="txt-marked">{{ $lang == 'en' ? 'English' : 'Arabic' }}</span>
                </h2>
                <p>
                    <span class="results-count">{{ $results->count() }}</span>
                    {{ $results->count() > 1 ? 'results' : 'result' }}
                </p>
            </div>
            @if ($results->count() == 0)
                <p class="empty-results">No results found</p>
            @else
                <div class="cards">
                    @foreach ($results as $result)
                        <div class="result">
                            <div class="result-card">
                                <div class="card-header">
                                    <p>
                                        @foreach ($result->tokenized_question as $token)
                                            @if ($result->marked_green->contains($token))
                                                <span class="txt-green">{{ $token }}</span>
                                            @elseif($result->marked_red->contains($token))
                                                <span class="txt-danger">{{ $token }}</span>
                                            @else
                                                {{ $token }}
                                            @endif
                                        @endforeach
                                    </p>
                                </div>
                                <div class="card-body">
                                    <p>
                                        @foreach ($result->tokenized_answer as $token)
                                            @if ($result->marked_green->contains($token))
                                                <span class="txt-green">{{ $token }}</span>
                                            @elseif($result->marked_red->contains($token))
                                                <span class="txt-danger">{{ $token }}</span>
                                            @else
                                                {{ $token }}
                                            @endif
                                        @endforeach
                                    </p>
                                </div>
                            </div>
                            @if (isset($result->rank))
                                <div class="rank primary-box">
                                    {{ $result->rank }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

        </section>
    @endif
@endsection

@section('scripts')
    <script>
        const searchBoxes = document.querySelector(".search-boxes");
        const addOrExpressionButton = document.querySelector(".btn-add-or-expression");
        const excludeAddButton = document.querySelector(".btn-add-not-expression");
        const vectorModalRadio = document.querySelector("input[type=radio]#vector-modal");
        const algorithmsRadios = document.querySelectorAll("input[type=radio][name='algorithm']");

        document.querySelector(".btn-add-or-expression").addEventListener("click", () => {
            searchBoxes.appendChild(createSearchBox("OR"));
        })
        excludeAddButton.addEventListener("click", () => {
            searchBoxes.appendChild(createSearchBox("NOT"));
            hideElement(excludeAddButton);
        })
        algorithmsRadios.forEach((element) => {
            element.addEventListener("click", () => {
                if (element.value == "vector-modal") {
                    document.querySelectorAll(".added-search-box").forEach((element) => {
                        element.remove();
                    });
                    hideElement(addOrExpressionButton);
                    hideElement(excludeAddButton);
                } else {
                    showElement(addOrExpressionButton);
                    showElement(excludeAddButton);
                }
            })
        })

        function createSearchBox(operator) {
            const newSearchBox = document.createElement("div");
            newSearchBox.classList.add("search-box", "added-search-box", "or-search-box");
            const operationText = document.createElement("p");
            operationText.classList.add("operation", "primary-box");
            operationText.textContent = operator;
            newSearchBox.append(operationText);
            const input = document.createElement("input");
            input.setAttribute("type", "text");
            input.setAttribute("required", "required");
            if (operator == "NOT") {
                input.setAttribute("name", "excludes[]");
            } else {
                input.setAttribute("name", "queries[]");
            }
            input.setAttribute("autocomplete", "off");
            newSearchBox.append(input);
            const button = document.createElement("button");
            button.setAttribute("type", "button");
            button.classList.add("btn-danger", "btn-delete-added-search-box");
            button.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                            </svg>
                            `;
            button.addEventListener("click", (e) => {
                e.target.closest(".added-search-box").remove();
                if (operator == "NOT") {
                    showElement(excludeAddButton);
                }
            })
            newSearchBox.append(button);
            return newSearchBox;
        }

        function showElement(element) {
            element.classList.remove("d-none");
        }

        function hideElement(element) {
            element.classList.add("d-none");
        }
    </script>
@endsection
