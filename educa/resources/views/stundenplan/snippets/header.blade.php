<style type="text/css">
    .customHeader {
        height: 45px;
        margin-left: 0;
        margin-right: 0;
    }
    #signature {
        max-height: 60px;
        width: auto;
    }
    #logo {
        max-height: 60px;
        width: auto;
    }
</style>
<div class="customHeader row justify-content-between">

    <div>
        <h2>{{ $displayName }}</h2>
    </div>
    <div>
        <table>
            <tr><td>Gültig bis:</td><td>vom {{ $startDate }} bis zum {{ $endDate }}</td></tr>
            <tr><td>Erstellt am:</td><td>{{ $created }}</td></tr>
        </table>
    </div>
</div>
