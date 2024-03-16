 <div class="attachments-sections ">
     <ul>
         @foreach ($document as $attachment)
             @if ($attachment->id_status == $status_id)
                 <li class='attach'>
                     <div class="thumb"><i class="mdi mdi-file-pdf"></i></div>
                     <div class="details">
                         <p class="file-name">
                             <a class='nav-link'
                                 @if (pathinfo($attachment->location, PATHINFO_EXTENSION) == 'zip' ||
                                         pathinfo($attachment->location, PATHINFO_EXTENSION) == 'rar' ||
                                         pathinfo($attachment->location, PATHINFO_EXTENSION) == '7z') href='/gallery/{{ $id }}/{{ $attachment->location }}/{{ $attachment->file_name }}'
                        @else href="/storage/files/{{ $id }}/{{ $attachment->location }}" target="_blank" @endif>
                                 <img src="/storage/img/{{ mb_substr($attachment->location, mb_strripos($attachment->location, '.') + 1, mb_strlen($attachment->location) - mb_strripos($attachment->location, '.')) }}.png"
                                     alt="{{ mb_substr($attachment->mime_type, mb_strpos($attachment->mime_type, '/'), strlen($attachment->mime_type) - mb_strpos($attachment->mime_type, '/')) }}"
                                     width=25px>

                                 {{ $attachment->file_name }} </a>
                         </p>
                         <div class="roww">
                             <div class="buttons">
                                 <p class="file-size">
                                     {{ round($attachment->file_size / 1024 / 1024, 2) }} мб

                                     <a onclick="if (!confirm('Точно удалить документ <{{ $attachment->file_name }}?>')) return false"
                                         href="/document/{{ $id }}/doc_delete/{{ $attachment->id }}"><img
                                             src="/storage/img/delete.png" width="35" alt="delete">
                                     </a>
                                     <a href="/storage/files/{{ $id }}/{{ $attachment->location }}"
                                         download><img src="/storage/img/download.png" width="35" alt="Скачать">
                                     </a>
                                 </p>

                             </div>
                             <div class="form-select">
                                 <form action="/doc_status_change" method="post" name='form{{ $attachment->id }}'>
                                     @csrf
                                     <input type="hidden" name="id" value="{{ $attachment->id }}">
                                     <div class="form-floating">
                                         <select class="form-control" id="doc_status" name="doc_status"
                                             onchange="form{{ $attachment->id }}.submit()">
                                             <option value="0" selected>
                                                 -------------
                                             </option>
                                             @foreach ($doc_statuses as $item)
                                                 <option value="{{ $item->id }}"
                                                     @if ($item->id === $attachment->id_status) selected @endif>
                                                     {{ $item->doc_status }}
                                                 </option>
                                             @endforeach

                                         </select>
                                         <label class ="label-form" for="doc_status">Тип документа</label>
                                     </div>
                                 </form>
                             </div>
                         </div>
                     </div>
                 </li>
             @endif
         @endforeach
     </ul>
 </div>
