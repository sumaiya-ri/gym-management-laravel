<h1>Services</h1>

<a href="/services/create">Add Service</a>

@foreach($services as $service)
    <p>{{ $service->name }} ({{ $service->duration }} mins)</p>
@endforeach