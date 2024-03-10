@extends('header_menu')
@section('title')
    Ответ в страховую 
@endsection
@section('main') 

{{-- 
<div class="alert" role="alert" style="width: 100%; background: rgb(116, 116, 255);">
   <div class="card-body">
    <h5 class="card-title"> 
   От: {{ $chat->from}}    
    </h5>
    <h5 class="card-title"> 
   Кому: {{ $chat->to}}    
    </h5>
    <h6 class="card-subtitle mb-2 text-muted">
        {{ $chat->created_at }}
    </h6>
    <p class="card-text">
       {{ $chat->message }}
    </p>
     </div>
</div>
   


<form action="/chat_send/{{$id}}" method="post">
@csrf

</form> --}}


<div class="container">
  
    <div class="row">

      <div class="col">

        <ul class="list-unstyled">
          
     
       <form action="/answer_ic_sent/{{$id}}" method="post">
        @csrf
        <div class="form-floating">
         <Select name="to" class="form-control">
                <option value="">Выберите получателя</option>
                    <option value="0">Все</option>
                @foreach ($users as $user)
                @if ($user->id!=Auth::user()->id)
                    
               
                    <option value="{{$user->id}}">{{$user->name}}</option>
                     @endif
                @endforeach
            </Select>
        </div>
        <input type="hidden" name="from" value="{{Auth::user()->id}}">
          <li class="bg-white mb-3">
           
            <div class="form-outline">
              <textarea class="form-control" id="textAreaExample2" rows="15" name=message>
Добрый день! 
По заявке {{$application->numberInsuranceCompanyApplication}} принято отрицательное решение по следующим причинам:

@foreach ($fail_vars as $item)
    -{{$item->variant}}
@endforeach

              </textarea>
              <label class="form-label" for="textAreaExample2">Message</label>
            </div>
          </li>
          <input type="submit" class="nav-link">
        </ul>
</form>
      </div>

    </div>

 </div>

@endsection