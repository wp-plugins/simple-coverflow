


jQuery(document).ready(function(){



    jQuery("#right").click(function(){
        if(jQuery(".simscoverflow").css('left')!=='0px'){    
            //alert(jQuery(".simscoverflow").css('left'));


            jQuery(".simscoverflow").animate({"left": "+=640px"}, "slow");
        }
    });




    simwidth='-'+jQuery(".simscoverflow").find('.gallery-item').length/4*640;
    //alert(simwidth);



    jQuery("#left").click(function(){
        w=jQuery(".simscoverflow").css('left').split('px');
        w=parseInt(w['0'])-640;
        if(w >= simwidth){    
            jQuery(".simscoverflow").animate({"left": "-=640px"}, "slow");

        }

    });


});