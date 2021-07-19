//検索条件変更の開閉
$(function(){
  //.SearchMenuの中の.SearchMenu_headerがクリックされたら
  $('.SearchMenu .SearchMenu_header').click(function(){
    //クリックされた.SearchMenuの中の.SearchMenu_headerに隣接するフォームが開いたり閉じたりする。
    $(this).next('.searchMenu_Inner').slideToggle();
    $(this).toggleClass("open");
  });
});
//検索ボタンを押した時のチェック
$(function(){
  $('.search').on('click', function(){
    if($('.Seisansya').val() == "選択してください" || $('.Hinsyu').val() == "選択してください" || $('.Toukyu').val() == "選択してください"){
      alert('検索条件はすべて選択してください');
      return false;
    }
  });
});
//モーダルウィンドウ開閉
$(function(){
  $('.modal-open').each(function(){
    $(this).on('click',function(){
      var target = $(this).data('target');
      var modal = document.getElementById(target);
      //米情報追加モーダルウィンドウ
      var re1 = $(modal).find('#RiceAdd');
      if(re1.length > 0){
        $(modal).find('#RiceAdd')[0].reset();
      }
      //入出荷情報追加モーダルウィンドウ
      var re2 = $(modal).find('#DataAdd');
      if(re2.length > 0){
        $(modal).find('#DataAdd')[0].reset();
      }
      //更新モーダルウィンドウ
      var re3 = $(modal).find('#UpData');
      if(re3.length > 0){
        $(modal).find('#UpData')[0].reset();
      }
      //詳細モーダルウィンドウ
      var re3 = $(modal).find('#details');
      if(re3.length > 0){
      }
      $(modal).fadeIn();
      return false;
    });
  });
  $('.modal-close').on('click',function(){
    $('.modal').fadeOut();
    return false;
  });
});
//入出荷追加フォーム 登録ボタン押した時のチェック
$(function(){
  $('.inventoryDataAdd').on('click', function(){
    if($('.Arrival').val() == "" && $('.Shipment').val() == ""){
      alert('入荷数か出荷数どちらかを入力してください。');
      return false;
    }
  });
});
//米情報登録フォーム生産者のその他を選択したとき
$(function(){
  $('.AddSeisansya').on('change', function(){
    $selectVal = $(this).val();
    if($selectVal ==="999"){
      $('.SeisansyaText').show();
    }else{
      $('.SeisansyaText').hide();
    }
  });
});