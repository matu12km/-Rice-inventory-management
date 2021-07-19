//初期起動時に生産者のプルダウンを最新のデータのみにする。
$(function(){
  var Nensan = $('#iNensan').val();
  //年産の値をselectSeisansya.phpへ渡す
  $.ajax({
    url: "selectSeisansya.php",
    type: "POST",
    data: { Nensan_id : Nensan },
    crossDomain: false,
    dataType: "json",
    scriptCharset: 'utf-8'
  }).done(function(data){
    //生産者のoptionの値を削除
    $('.Seisansya option').remove();
    $('.Seisansya').append($('<option>').text("選択してください").attr('selected',true));
    $.each(data, function(seisansya_ID, Seisansya_NAME){
      $('.Seisansya').append($('<option>').text(Seisansya_NAME).attr('value', seisansya_ID));
    })
  }).fail(function (XMLHttpRequest, textStatus, errorTrown) {
    alert("errorThrown    : " + errorTrown.message); // 例外情報
  });
})

//年産が変更された場合
$(function(){
  $('.Nensan').on('change',function(){
    var Nensan = $(this).val();
    //年産の値をselect.phpへ渡す
    $.ajax({
      url: "selectSeisansya.php",
      type: "POST",
      data: { Nensan_id : Nensan },
      crossDomain: false,
      dataType: "json",
      scriptCharset: 'utf-8'
    }).done(function(data){
      //生産者のoptionの値を削除
      $('.Seisansya option').remove();
      $('.Seisansya').append($('<option>').text("選択してください").attr('selected',true));
      $.each(data, function(seisansya_ID, Seisansya_NAME){
        $('.Seisansya').append($('<option>').text(Seisansya_NAME).attr('value', seisansya_ID));
      })
    }).fail(function (XMLHttpRequest, textStatus, errorTrown) {
      alert("errorThrown    : " + errorTrown.message); // 例外情報
    });
  });
});

//生産者が変更されたら品種のリストを作成する。
$(function(){
  $('.Seisansya').on('change',function(){
    var nensan = $('.Nensan option:selected').val();
    var seisansyaID = $(this).val();
    //生産者の値をselect.phpへ渡す
    $.ajax({
      url: "selectHinsyu.php",
      type: "POST",
      data: {
        nensan_id : nensan,
        seisansya_id : seisansyaID },
      crossDomain: false,
      dataType: "json",
      scriptCharset: 'utf-8'
    }).done(function(data){
      //等級のoptionの値を削除
      $('.Hinsyu option').remove();
      $('.Hinsyu').append($('<option>').text("選択してください").attr('selected',true));
      //select.php空の値をoptionに使用
      $.each(data, function(Hinsyu_ID, Hinsyu_NAME){
        $('.Hinsyu').append($('<option>').text(Hinsyu_NAME).attr('value', Hinsyu_ID));
      })
      //品種を操作可能にする。品種は操作不可にする。
      document.getElementById("iHinsyu").disabled = false;
      if(document.getElementById("iToukyu").disabled == false){
        document.getElementById("iToukyu").disabled = true;
      }
    }).fail(function (XMLHttpRequest, textStatus, errorTrown) {
      alert("errorThrown    : " + errorTrown.message); // 例外情報
    });
  });
});

//品種が変更されたら等級リストを作成する。
$(function(){
  $('.Hinsyu').on('change',function(){
    var nensan = $('.Nensan option:selected').val();
    var seisansyaID = $('.Seisansya option:selected').val();
    var hinsyuID = $(this).val();
    //生産者の値をselectHinsyu.phpへ渡す
    $.ajax({
      url: "selectToukyu.php",
      type: "POST",
      data: {
        nensan_id : nensan,
        hinsyu_id : hinsyuID ,
        seisansya_id : seisansyaID
      },
      crossDomain: false,
      dataType: "json",
      scriptCharset: "utf-8"
    }).done(function(data){
      //optionの値を削除
      $('.Toukyu option').remove();
      //select.php空の値をoptionに使用
      $('.Toukyu').append($('<option>').text("選択してください").attr('selected',true));
      $.each(data, function(Toukyu_ID, Toukyu){
        $('.Toukyu').append($('<option>').text(Toukyu).attr('value', Toukyu_ID));
      })
      //等級を操作可能にする。
      document.getElementById("iToukyu").disabled = false;
    }).fail(function (XMLHttpRequest, textStatus, errorTrown) {
      console.log("XMLHttpRequest : " + XMLHttpRequest.text);
      console.log("textStatus     : " + textStatus);    // タイムアウト、パースエラー
      alert("errorThrown    : " + errorTrown.message); // 例外情報
    });
  });
});