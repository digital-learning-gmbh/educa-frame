<!-- As a heading -->
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <span class="navbar-brand mb-0 h1">{{ $dokument->name }}</span>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Datei
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item" href="/dokument/{{ $dokument->id }}/download">Download</a>
                    <a class="dropdown-item" href="/dokument/{{ $dokument->id }}/delete">Löschen</a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<style>
    body {
        padding-top: 55px;
        margin: 0px;
    }
</style>
