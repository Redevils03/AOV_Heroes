var sorting = document.getElementById("sort");

$("#submit").click( function () {
  $("#data_show").html("");
  $.get("hero.php?action=read&sort="+ sorting.value,  function (response) {
    var panjang = response.length;
    if (panjang == 0) {
      const card = document.createElement('div');
      card.classList.add('col');
      card.innerHTML = `
      <div class="text-center">
        <h2>Hero Not Found</h2>
      </div>`;
      $("#data_show").html(card);
      
    } else {
      for (var i = 0; i < panjang; i++) {
        $("#data_show").append(
            "<div class='col-12 col-md-6 col-lg-3 col-xxl-2'>" +
            "<div class='card mb-4' style='width:250px;'>" +
                "<img class='card-img-top' src='./img/" + response[i]["id_hero"] + ".jpg' style='object-fit:cover; object-position:center; width:auto; height:300px;'>" +
                "<div class='card-body'>" +
                    "<a href='detail_hero.html?action=detail&id_hero="+ response[i]["id_hero"] +"'><h5 class='card-title'>"+ response[i]["nama_hero"] +"</h5></a>" +
                    "<p class='card-text'>" +
                        "<div class='float-end'>" +
                            "<a href='form.html?action=update&id_hero="+ response[i]["id_hero"] +"' class='btn btn-success btn-sm' style='margin-right: 5px; color: white;'><i class='bi bi-pencil-square'></i></a>" +
                            "<a href='hero.php?action=delete&id_hero="+ response[i]["id_hero"] +"' class='btn btn-danger btn-sm' style='color: white;'><i class='bi bi-trash3-fill'></i></a>" +
                        "</div>"+ 
                    "</p>" +
                "</div>" +
            "</div>" +
        "</div>");
      }
    }
  });
}); 