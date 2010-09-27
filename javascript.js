
simple_coverflow={


    "run":function simple_coverflow (i,simple_cover_flow_id,simple_cover_content_width,div_id,width_of_all_img){

        
        jQuery("#right_"+i).click(function(){

            //alert(i+'hej');

            if(jQuery(div_id).css('left')!=='0px' && jQuery(div_id).css('left')!=='auto'){    
                
                jQuery(div_id).animate({"left": "+="+simple_cover_content_width+"px"}, "slow");

            }

        });








        jQuery("#left_"+i).click(function(){  

            
            findWidth=Array();
            findWidth=jQuery(div_id).css('left').split('px');
            
            
            if(findWidth['0']=='auto'){    //ie 
               findWidth['0']=0;
            }
            
            w=parseInt(findWidth['0'])-simple_cover_content_width;
            
            if(w > width_of_all_img){    

                jQuery(div_id).animate({"left": "-="+simple_cover_content_width+"px"}, "slow");

            }

        });

        
    }   

};

jQuery(document).ready(function(){
    numOfCoverflows=jQuery('#content').find('.simple_coverflow').length;
    obj_simple_coverflow=Array();
    for (i=0;i<=numOfCoverflows;i++){   

        obj_simple_coverflow[i]=simple_coverflow;
        div_id='#simple_coverflow-'+i+' '+simple_cover_flow_id;
        width_of_all_img='-'+jQuery(div_id).find('.simple_coverflow-item').length/4*simple_cover_content_width;
        obj_simple_coverflow[i].run(i,simple_cover_flow_id,simple_cover_content_width,div_id,width_of_all_img);

    }

});

