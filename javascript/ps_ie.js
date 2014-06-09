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
    var win_h = $(window).height() - 140; 

    var left_w = win_w * 0.5; // default width of left pane.

    $("#container").width ( win_w );
    $("#container").height( win_h );

    $("#div1").width(left_w);
    $("#div1").height(win_h / 3 - 10);

    $("#dragbar1").width(left_w);

    $("#div2").width(left_w);
    $("#div2").height(win_h / 3 - 10);

    $("#dragbar2").width(left_w);

    $("#div3").width(left_w);
    $("#div3").height(win_h / 3);

    $("#dragbarV").height( win_h );
    $("#dragbarV").offset({ left: left_w + 15 });

    $("#divR").offset({ left: left_w + 25 }); 
    $("#divR").width( win_w - (left_w + 30) );
    $("#divR").height( win_h );
  }

  $(window).resize(function() { init(); });

  $("#dragbar1").mousedown(function(e){
    drag1 = true;
    h_total = $("#div1").height() + $("#div2").height();
    pos0 = $("#div1").offset().top;
  });

  $("#dragbar2").mousedown(function(e){
    drag2 = true;
    h_total = $("#div3").height() + $("#div2").height();
    pos0 = $("#div2").offset().top;
  });

  $("#dragbarV").mousedown(function(e){
    dragV = true;
  });


  $(document).mousemove(function(e){
    var posx = 0, posy = 0;

    if (drag1 || drag2 || dragV) {
        var win_w = $(window).width() - 30;
        var win_h = $(window).height() - 100; 

        if (e.pageX || e.pageY) {
            posx = e.pageX;
            posy = e.pageY;
        }
        else if (e.clientX || e.clientY) {
            posx = e.clientX + document.body.scrollLeft
                   + document.documentElement.scrollLeft;
            posy = e.clientY + document.body.scrollTop
                   + document.documentElement.scrollTop;
        }
    }

    //$("#msg").html( posy );

    if (drag1) {
        var h1 = posy - pos0; 
        if ( h1 < 50 || h1 > h_total - 50 ) return;

        $("#div1").height( h1 );
        $("#div2").height( h_total - h1 + 24 );
    }

    if (drag2) {
        var h1 = posy - pos0; 
        if ( h1 < 50 || h1 > h_total - 50 ) return;

        $("#div2").height( h1 );
        $("#div3").height( h_total - h1 + 24 );
    }

    if (dragV) {
        var w1 = posx - 15; //23;
        if ( w1 < 300 || w1 > win_w - 200 ) return;

        $("#div1").width( w1 ); 
        $("#dragbar1").width( w1 );
        $("#div2").width( w1 );
        $("#dragbar2").width( w1 );
        $("#div3").width( w1 );

        $("#dragbarV").offset({ left: posx });

        $("#divR").width( win_w - w1 - 25 );
        $("#divR").offset({ left: w1 + 32 });
    }
  });

  $(document).mouseup(function(e){
    drag1 = false;
    drag2 = false;
    dragV = false;
  });

});

