
simple_coverflow={
    

    "run":function simple_coverflow (i,simple_cover_flow_id,simple_cover_content_width,div_id,width_of_all_img){
    
        //alert(i);

        jQuery("#right_"+i).click(function(){

            //alert(i+'hej');

            if(jQuery(div_id).css('left')!=='0px'){    
                //alert(jQuery(".simscoverflow").css('left'));

                // alert(div_id);
                jQuery(div_id).animate({"left": "+="+simple_cover_content_width+"px"}, "slow");

            }

        });





        


        jQuery("#left_"+i).click(function(){  

            //alert("#left_"+i);


            w=jQuery(div_id).css('left').split('px');
            w=parseInt(w['0'])-simple_cover_content_width;
            //    jQuery(div_id).animate({"left": "-="+simple_cover_content_width+"px"}, "slow");

            if(w >= width_of_all_img){    



                jQuery(div_id).animate({"left": "-="+simple_cover_content_width+"px"}, "slow");

            }

        });




    }    



};

jQuery(document).ready(function(){
    numOfCoverflows=jQuery('#content').find('.simple_coverflow').length;

    for (i=0;i<=numOfCoverflows;i++){   

        //g[i]=new simple_coverflow;

        obj_simple_coverflow=simple_coverflow;
        div_id='#simple_coverflow-'+i+' '+simple_cover_flow_id;
        width_of_all_img='-'+jQuery(div_id).find('.simple_coverflow-item').length/4*simple_cover_content_width;
        
        obj_simple_coverflow.run(i,simple_cover_flow_id,simple_cover_content_width,div_id,width_of_all_img);

    }

})(jQuery);
