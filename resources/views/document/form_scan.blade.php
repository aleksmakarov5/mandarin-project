 <form action="/documents/{{ $id }}/priemka_add" method="post" enctype="multipart/form-data"
     style='display:inline;'>
     @csrf <!-- с версии Laravel 5.6 -->
     @if (isset($load_name))
         <input type="hidden" name="load_name" value="{{ $load_name }}">
         <input type="hidden" name="load_location" value="{{ $load_location }}">
         <input type="file" name="userfile[]" multiple accept="image/png, image/gif, image/jpeg"
             onchange="this.form.submit()">
     @else
         <input type="file" name="userfile[]" multiple accept="application/pdf" onchange="this.form.submit()">
     @endif

     <input type="hidden" name="status_id" value="{{ $status_id }}">

 </form>
