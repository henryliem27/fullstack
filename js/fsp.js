$("#tambah").click(function(){
    $("#foto").append('<input type="file" name="foto"><br>');
});

$("#btnPemain").click(function(){
    var strPemain;

    var nPemain = $("#pemain option:selected").text();
    var nPeran = $("#peran").val();
    var idPemain = $("#pemain").val();

    strPemain = "<tr>";
    strPemain = strPemain + "<td>"+ nPemain + "</td>";
    strPemain = strPemain + "<td>"+ nPeran+ "</td>";
    strPemain = strPemain + "<td><input type='button' value='Hapus' class='btnHapus'></td>";
    strPemain = strPemain + "<input type='hidden' name='pemain[]' value='"+ idPemain +"'>";
    strPemain = strPemain + "<input type='hidden' name='peran[]' value='"+ nPeran +"'>";
    strPemain = strPemain + "</tr>";

    $("#daftarPemain").append(strPemain);
});

$("document").on("click", ".btnHapus", function(){
    $(this).parent().parent().remove();
});