//
// @By: Xin Chen
// @Created on: 4/23/2013
// @Last modified: 4/23/2013
//

var drag1 = false;
var drag2 = false;
var dragV = false;
var pos0;
var h_total;

jQuery(document).ready(function() {

  init();

  function init() {
    var win_w = $(window).width() - 10;
    var win_h = $(window).height() - 100; // - this.offsetTop;
    //var win_h = $(document).height();

    var left_w = win_w * 0.5; // 400; // default width of left pane.

    $("#container").width ( win_w );
    $("#container").height( win_h );

    $("#div1").width(left_w - 8);
    $("#div1").height(win_h / 3 - 15);

    $("#dragbar1").width(left_w + 2);

    $("#div2").width(left_w - 8);
    $("#div2").height(win_h / 3 - 15);

    $("#dragbar2").width(left_w + 2);

    $("#div3").width(left_w - 8);
    $("#div3").height(win_h / 3 - 15);

    $("#dragbarV").height( win_h );
    $("#dragbarV").offset({ left: left_w + 15 });

    $("#divR").offset({ left: left_w + 25 });
    $("#divR").width( win_w - (left_w + 30) );
    $("#divR").height( win_h - 15 );
  }

  $(window).resize(function() { init(); });

  $("#dragbar1").mousedown(function(e){
    drag1 = true;
    h_total = $("#div1").height() + $("#div2").height();
    pos0 = e.pageY; 
  });

  $("#dragbar2").mousedown(function(e){
    drag2 = true;
    h_total = $("#div3").height() + $("#div2").height();
    pos0 = e.pageY; 
  });

  $("#dragbarV").mousedown(function(e){
    dragV = true;
    pos0 = e.pageX;
  });


  $(document).mousemove(function(e){
    var win_w = $(window).width() - 30;
    var win_h = $(window).height() - 100; // - this.offsetTop;

    if (drag1) {
        var v = e.pageY - pos0; 
        pos0 = e.pageY;

        var h1 = $("#div1").height() + v;
        if ( h1 < 50 || h1 > h_total - 50 ) return;

        $("#div1").height($("#div1").height() + v);
        $("#div2").height($("#div2").height() - v);
    }

    if (drag2) {
        var v = e.pageY - pos0; 
        pos0 = e.pageY;

        var h1 = $("#div2").height() + v;
        if ( h1 < 50 || h1 > h_total - 50 ) return;

        $("#div2").height($("#div2").height() + v);
        $("#div3").height($("#div3").height() - v);
    }

    if (dragV) {
        var v = e.pageX - pos0; 
        pos0 = e.pageX;

        var w1 = $("#div1").width() + v;
        if ( w1 < 300 || w1 > win_w - 100 ) return;

        $("#div1").width($("#div1").width() + v);
        $("#dragbar1").width($("#dragbar1").width() + v);
        $("#div2").width($("#div2").width() + v);
        $("#dragbar2").width($("#dragbar2").width() + v);
        $("#div3").width($("#div3").width() + v);

        $("#dragbarV").offset({ left: $("#dragbarV").offset().left + v });

        $("#divR").width($("#divR").width() - v);
        $("#divR").offset({ left: $("#divR").offset().left + v });
    }
  });

  $(document).mouseup(function(e){
    drag1 = false;
    drag2 = false;
    dragV = false;
  });

});