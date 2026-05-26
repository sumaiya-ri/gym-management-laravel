<h1>Create Service</h1>

<form method="POST" action="/services">
    @csrf

    <input type="text" name="name" placeholder="Service Name">
    <input type="number" name="duration" placeholder="Duration">

    <button type="submit">Save</button>
</form>