<style type='text/css'>
#browser_box{
   border: 1px solid #C9D7F1;
   width:800px;
   padding:5px;
   overflow:auto;
   margin-left:auto;
   margin-right:auto;
}
   
.items_div
{
   width: 150px;
   height: 150px;
   overflow:auto;
   padding:5px;
   overflow: -moz-scrollbars-vertical;
   overflow-x: auto;
   border:1px solid silver;
}

.items
{
   list-style-type: none;
   margin:auto;
}
.items li{
   padding:5px;
   color:black;
   float:left;
}

.items div:hover{
   background-color:whitesmoke;
   /*
   background-image:url(folder-open.png);
   */
}

.normal_folder{
   padding:1px;
   color:black;
   text-align:center;
   border: 1px solid #C9D7F1;
   padding:5px;
   min-width:60px;
   /*
   background-image:url(folder.png);
   background-size:auto;
   width:64px;
   height:64px;
   */

}

.selected_folder{
   padding:5px;
   background-color:#C9D7F1;
   color:blue;
   min-width:60px;
   text-align:center;
   border:1px solid silver;
   /*
   background-image:url(folder-open.png);
   background-size:auto;
   width:64px;
   height:64px;
   */
}


.shadow,.selected_folder {
   -moz-background-clip:border;
   -moz-background-inline-policy:continuous;
   -moz-background-origin:padding;
   -moz-box-shadow:0 4px 8px #C8C8C8;

  /*None mozilla*/
   -webkit-box-shadow: #C8C8C8 0px 4px 8px;
   -webkit-box-shadow: #C8C8C8 0px 4px 8px;
  /*None mozilla*/
}

.round,.items div,.browser,#browser_box{
   -moz-border-radius-bottomleft:5px;
   -moz-border-radius-bottomright:5px;
   -moz-border-radius-topleft:5px;
   -moz-border-radius-topright:5px;

   border-bottom-left-radius: 5px 5px;
   border-bottom-right-radius: 5px 5px;
   border-top-left-radius: 5px 5px;
   border-top-right-radius: 5px 5px;

}

.clean{
   border-collapse:collapse;
   border:1px solid black;
}

.clean td{
   padding:5px;
}

.color0{
   color:black;
}
.color1{
   color:blue;
}
.color2{
   color:green;
}
.color3{
   color:orange;
}
.color4{
   color:red;
}

.ul_menu {
   list-style-type: none;
   margin-left:auto;
   margin-right:auto;
}

.ul_menu a{
   padding-right: 10px;
   padding-left: 10px;
   text-decoration: none;
} 

.ul_menu li {
   float: left;
}

.summery{
   height:320px;
   overflow:auto;
   border:1px solid #C9D7F1;
   padding:10px;
}

.detail{
   height:320px;
   overflow:auto;
   border:1px solid #C9D7F1;
   padding:10px;
}
.data_table{
   width:80%;
   margin-left:auto;
   margin-right:auto;
}
.data_table td{
   vertical-align:top;
}

</style>
