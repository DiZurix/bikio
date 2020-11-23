$( function() {
  $( "#dialog" ).dialog({
    autoOpen: false,
    show: {
      effect: "blind",
      duration: 1
    },
  });

  $( "#opener" ).on( "click", function() {
    $( "#dialog" ).dialog( "open" );
  });
} );