@if($errors->any()) 
    <x-adminlte-callout theme="danger" title="Field's is required">
     {{ $errors }}
    </x-adminlte-callout>
@endif

@if (session()->has('success'))
 <x-adminlte-card theme="lime" theme-mode="outline">
    {{session()->get('success')}}
 </x-adminlte-card>
@endif
