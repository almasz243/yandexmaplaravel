<?php
use Illuminate\Support\Facades\Auth;
?>
    <!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://api-maps.yandex.ru/2.1/?apikey=be203022-19da-4b49-8bcb-5ca04a8cb7ea&lang=ru_RU" type="text/javascript"></script>
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    <title>Document</title>
</head>
<body>
    <p>Здравствуйте, {{Auth::user()->email}}</p>
    <form action="{{route('user.logout')}}" method="GET">
        @csrf
        <button type="submit" class="btn btn-outline-secondary">Выйти</button>
    </form>
    <hr>
        <div class="row">
            <div class="col-3">
                <form action="{{route('user.post')}}" method="POST">
                    @csrf
                    <div class="input-group">
                        <input type="text" id="name" name="name" placeholder="Название" class="form-control">
                        <input type="text" id="latitude" name="latitude" placeholder="Долгота" class="form-control">
                        <input type="text" id="longitude" name="longitude" placeholder="Широта" class="form-control">
                        <button class="btn btn-outline-secondary" id="button">Добавить</button>
                    </div>
                </form>
                @if(session()->has('message'))
                    <div>
                        {{ session()->get('message') }}
                    </div>
                @endif
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                @foreach($results as $result)
                    <form action="{{route('user.edit')}}" method="GET">
                        @csrf
                        <div onclick="link{{$result->id}}()" id="editable{{$result->id}}">{{$result->name}}: {{$result->latitude}}/{{$result->longitude}}</div>
                        <div class="input-group">
                            <input type="text" style="display:none" value="{{$result->id}}" name="id" class="form-control">
                            <input type="text" style="display:none" value="{{$result->userid}}" name="userid">
                            <input type="text" style="display:none" value="{{$result->name}}" name="name" id="editName{{$result->id}}" class="form-control">
                            <input type="text" style="display:none" value="{{$result->latitude}}" name="latitude" id="editLatitude{{$result->id}}" class="form-control">
                            <input type="text" style="display:none" value="{{$result->longitude}}" name="longitude" id="editLongitude{{$result->id}}" class="form-control">
                            <input id="edit{{$result->id}}" type="button" class="btn btn-outline-secondary" value="Редактировать">
                            <input id="submit{{$result->id}}" style="display:none" class="btn btn-outline-secondary" type="submit" value="Принять">
                        </div>
                    </form>
                    <form action="{{route('user.delete')}}" method="GET">
                        @csrf
                        <input type="text" style="display:none" value="{{$result->id}}" name="id">
                        <input type="text" style="display:none" value="{{$result->userid}}" name="userid">
                        <Button class="btn btn-outline-secondary">Удалить</Button>
                    </form>
                @endforeach
            </div>
            <div class="col-9">
                <div id="premap" style="width: 100%; height: 100vh"></div>
                <div id="map" style="width: 100%; height: 100vh"></div>
                <div id="parent"></div>
            </div>
        </div>
</body>
<script type="text/javascript">
    var div = document.getElementById('premap')
    var parent = document.getElementById('parent')
    var button = document.getElementById('button')
    var name = document.getElementById('name')
    var column = document.getElementById('column')
    var newLatitude = document.getElementById('latitude')
    var newLongitude = document.getElementById('longitude')
    @foreach($results as $result)
    var editButton{{$result->id}} = document.getElementById('edit{{$result->id}}')
    var submit{{$result->id}} = document.getElementById('submit{{$result->id}}')
    var editDiv{{$result->id}} = document.getElementById('editable{{$result->id}}')
    var editName{{$result->id}} = document.getElementById('editName{{$result->id}}')
    var editLatitude{{$result->id}} = document.getElementById('editLatitude{{$result->id}}')
    var editLongitude{{$result->id}} = document.getElementById('editLongitude{{$result->id}}')
    editButton{{$result->id}}.onclick = function(){
        editDiv{{$result->id}}.remove()
        editName{{$result->id}}.style.display = "block"
        editLatitude{{$result->id}}.style.display = "block"
        editLongitude{{$result->id}}.style.display = "block"
        editButton{{$result->id}}.remove()
        submit{{$result->id}}.style.display = "block"
    }
    function link{{$result->id}}(){
        ymaps.ready(init);
        var oldmap = document.getElementById('map')
        oldmap.remove();
        parent.insertAdjacentHTML('afterbegin', "<div id='map' class='col-9' style='width: 100%; height: 100vh'></div>");
        function init(){
            var myMap = new ymaps.Map("map", {
                center: [{{$result->latitude}}, {{$result->longitude}}],
                zoom: 7
            });
            @foreach($results as $result)
            var {{$result->name}} = new ymaps.Placemark([{{$result->latitude}}, {{$result->longitude}}],{
                balloonContent: "{{$result->name}}"
            });
            myMap.geoObjects.add({{$result->name}});
            @endforeach
        }
    }
    @endforeach
    navigator.geolocation.getCurrentPosition(
        successCallback,
        errorCallback
    );
    //эта карта чтобы отрисовывать карту до того как юзер примет или отклонит запрос о получении геолокации
    ymaps.ready(init);
    function init(){
        var myMap = new ymaps.Map("premap", {
            center: [55.76, 37.64],
            zoom: 7
        });
        @foreach($results as $result)
        var {{$result->name}} = new ymaps.Placemark([{{$result->latitude}}, {{$result->longitude}}],{
            balloonContent: "{{$result->name}}"
        });
        myMap.geoObjects.add({{$result->name}});
        @endforeach
    }
    button.onclick = function (){
        var object = new ymaps.GeoObject({
            geometry: {
                type: "Point", // тип геометрии - точка
                coordinates: [newLatitude, newLongitude] // координаты точки
            }
        });
        myMap.geoObjects.add(object);
    }
    //если он принял то отрисовывается карта по его местоположению
    function successCallback(position) {
        const { latitude, longitude } = position.coords;
        ymaps.ready(init);
        div.remove()
        function init(){
            var myMap = new ymaps.Map("map", {
                center: [latitude, longitude],
                zoom: 7
            });
            @foreach($results as $result)
            var {{$result->name}} = new ymaps.Placemark([{{$result->latitude}}, {{$result->longitude}}],{
                balloonContent: "{{$result->name}}"
            });
            myMap.geoObjects.add({{$result->name}});
            @endforeach
        }
    }
    //если он отклонил то отрисовывается карта Москвы
    function errorCallback(){
        ymaps.ready(init);
        div.remove()
        function init(){
            var myMap = new ymaps.Map("map", {
                center: [55.76, 37.64],
                zoom: 7
            });
            @foreach($results as $result)
            var {{$result->name}} = new ymaps.Placemark([{{$result->latitude}}, {{$result->longitude}}],{
                balloonContent: "{{$result->name}}"
            });
            myMap.geoObjects.add({{$result->name}});
            @endforeach
        }
    }
</script>
</html>
