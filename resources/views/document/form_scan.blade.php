 <form action="/documents/{{ $id }}/priemka_add" method="post" enctype="multipart/form-data"
     style='display:inline;'>
     @csrf <!-- с версии Laravel 5.6 -->
     <input type="file" name="userfile[]" multiple accept="image/png, image/gif, image/jpeg"
         onchange="this.form.submit()">


 </form>
 @include('document/attach')
