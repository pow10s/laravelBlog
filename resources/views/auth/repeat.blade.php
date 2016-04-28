<form method="POST">
    <label for="email">Your e-mail</label>
    <input type="email" name="email"/>
    <input type="hidden" name="_token" value="{{csrf_token()}}"/>
    <br>
    <input type="submit" value="Request"/>
</form>
@if(Session::has('message'))
    {{Session::get('message')}}
@endif