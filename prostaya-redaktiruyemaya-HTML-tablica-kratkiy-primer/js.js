function tableSearch(k,table) {
    var phrase = document.getElementById('search-text'+k);
    var table = document.getElementById(table);
    var regPhrase = new RegExp(phrase.value, 'i');
    var flag = false;
    for (var i = 2; i < table.rows.length; i++) {
        flag = false;
        flag = regPhrase.test(table.rows[i].cells[k].innerHTML);
        if (flag) {
            table.rows[i].style.display = "";
        } else {
            table.rows[i].style.display = "none";
        }

    }
}
// ИНИЦИАЛИЗАЦИЯ - ДВОЙНОЙ ЩЕЛЧОК ДЛЯ РЕДАКТИРОВАНИЯ ЯЧЕЙКИ
window.addEventListener("DOMContentLoaded", () => {
for (let cell of document.querySelectorAll("#demo td")) {
cell.ondblclick = () => { editable.edit(cell); };
}
});
let editable = {
// "ПРЕОБРАЗОВАТЬ" В РЕДАКТИРОВАННУЮ ЯЧЕЙКУ
edit: (cell) => {
// УДАЛИТЬ "ДВОЙНОЙ ЩЕЛЧОК ДЛЯ РЕДАКТИРОВАНИЯ"
cell.ondblclick = "";
// РЕДАКТИРУЕМОЕ СОДЕРЖИМОЕ
cell.contentEditable = true;
cell.focus();
// "ОТМЕТИТЬ" ТЕКУЩУЮ ВЫБРАННУЮ ЯЧЕЙКУ
        cell.classList.add("edit");
editable.selected = cell;
// НАЖМИТЕ ENTER ИЛИ ЩЕЛКНИТЕ ВНЕ, ЧТОБЫ ЗАВЕРШИТЬ РЕДАКТИРОВАНИЕ
window.addEventListener("click", editable.close);
cell.onkeydown = (evt) => { if (evt.key=="Enter") {
editable.close(true);
return false;
}};
},
// ЗАВЕРШИТЬ "РЕЖИМ РЕДАКТИРОВАНИЯ"
selected: null,  // текущая выбранная ячейка
close: (evt) => { if (evt.target != editable.selected) {
 
// УДАЛИТЬ "РЕДАКТИРОВАНИЕ"
window.getSelection().removeAllRanges();
editable.selected.contentEditable = false;
// ВОССТАНОВИТЬ КЛИК-СЛУШАТЕЛИ
    window.removeEventListener("click", editable.close);
    
let cell = editable.selected;
cell.ondblclick = () => { editable.edit(cell); };
// "СНЯТЬ ПОМЕТКУ" ТЕКУЩЕЙ ВЫБРАННОЙ ЯЧЕЙКИ
editable.selected.classList.remove("edit");
    editable.selected = null;
    // newvar = "Третий способ вывода переменной";
    let str = cell.id;
    let start = str.indexOf("_");
    let isid = document.getElementById(str.substring(start+1, str.length));
    isid.value = cell.innerHTML; 
}}
};

