
simple_coverflow={


    "run":function simple_coverflow (i,simple_cover_flow_id,simple_cover_content_width,div_id,width_of_all_img){


        jQuery("#right_"+i).click(function(){

            //alert(i+'hej');

            if(jQuery(div_id).css('left')!=='0px' && jQuery(div_id).css('left')!=='auto'){    

                jQuery(div_id).animate({"left": "+="+(parseInt(simple_cover_content_width)+parseInt(simple_cover_border))+"px"}, "slow");

            }

        });



        jQuery("#simple_coverflow-"+i).mouseover(function(){  
            jQuery("#buttons_"+i).css('display','block');

        });

        jQuery("#simple_coverflow-"+i).mouseout(function(){  
            jQuery("#buttons_"+i).css('display','none');

        });




        jQuery("#left_"+i).click(function(){  


            findWidth=Array();
            findWidth=jQuery(div_id).css('left').split('px');


            if(findWidth['0']=='auto'){    //ie 
                findWidth['0']=0;
            }

            w=parseInt(findWidth['0'])-simple_cover_content_width;

            if(w > width_of_all_img){    

                jQuery(div_id).animate({"left": "-="+(parseInt(simple_cover_content_width)+parseInt(simple_cover_border))+"px"}, "slow");

            }

        });


    }   

};

jQuery(document).ready(function(){
    numOfCoverflows=jQuery('#content').find('.simple_coverflow').length;
    
    /**
    * 
    */
    /*
    jQuery('.simple_coverflow a').click(function(){
        jQuery('#frame-1 img').attr('src',jQuery(this).attr('href'));
        return false;
    })*/ 
    
    obj_simple_coverflow=Array();
    for (i=0;i<=numOfCoverflows;i++){   

        obj_simple_coverflow[i]=simple_coverflow;
        div_id='#simple_coverflow-'+i+' '+simple_cover_flow_id;
        width_of_all_img='-'+jQuery(div_id).find('.simple_coverflow-item').length/4*simple_cover_content_width;
        obj_simple_coverflow[i].run(i,simple_cover_flow_id,simple_cover_content_width,div_id,width_of_all_img);

    }

});

