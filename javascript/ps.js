//
// @By: Xin Chen
// @Created on: 4/23/2013
// @Last modified: 4/30/2013
//

var dragV = false;
var pos0;
var h_total;

jQuery(document).ready(function() {

  init();

  function init() {
    var win_w = $(window).width() - 25;
    var win_h = $(window).height() - 250; // - this.offsetTop;
    //var win_h = $(document).height();

    var left_w = 400; // default width of left pane.

    $("#container").width ( win_w );
    $("#container").height( win_h );

    $("#divL").width(left_w - 8);
    $("#divL").height( win_h );

    $("#dragbarV").height( win_h );
    $("#dragbarV").offset({ left: left_w + 15 });

    $("#divR").offset({ left: left_w + 25 });
    $("#divR").width( win_w - (left_w + 30) );
    $("#divR").height( win_h );
  }

  $(window).resize(function() { init(); });

  $("#dragbarV").mousedown(function(e){
    dragV = true;
    pos0 = e.pageX;
  });


  $(document).mousemove(function(e){
    var win_w = $(window).width() - 25;
    var win_h = $(window).height() - 250; // - this.offsetTop;
    if (dragV) {
        var v = e.pageX - pos0; 
        pos0 = e.pageX;

        var w1 = $("#divL").width() + v;
        if ( w1 < 300 || w1 > win_w - 100 ) return;

        $("#divL").width(w1); //$("#divL").width() + v);

        $("#dragbarV").offset({ left: $("#dragbarV").offset().left + v });

        $("#divR").width($("#divR").width() - v);
        $("#divR").offset({ left: $("#divR").offset().left + v });
    }
  });

  $(document).mouseup(function(e){
    dragV = false;
  });

});
