<?php 
include"db.php";
error_reporting(E_ERROR || E_PARSE );

$user = $_POST["user"];
$date = $_POST["date"];


$nameOfday= date('D', strtotime($date));

$category = $_POST["ketegori"];
$ticket = (int)$_POST["tiket"];
$submit = $_POST["submit"];
$price = null;
$diskon = 0;
$weekend = null;
$result = null;
$wekday = null;


if (isset($submit) && isset($user) && isset($category)) {



 if ($nameOfday == "Sun" || $nameOfday ==  "Sat" ) {
   $weekend = true;
   $wekday = "Weekday";
 }else{
    $weekend = false;
    $wekday = "Weekend";
 }



 switch ($category) {
    case "domestik": 
        if ($weekend) {
            $price = 19800000;
        }else{
            $price = 18500000;
        }
        break;
    case "internasional": 
        if ($weekend) {
            $price = 50000000;
        }else{
            $price = 40000000;
        }
        break;


 



   


}




if ($ticket >= 10) {
    $diskon = 20;
}elseif($diskon >= 5){
    $diskon = 10;
}





if ($diskon) {
    
    $result = ($price * $diskon) / 100;
}else{
    $result = $price;
}


$query = "INSERT INTO result(user,kategori,tanggal,ticket,price,diskon,result,weekend) VALUES('$user','$category','$date','$ticket','$price','$diskon','$result','$wekday')";
$sql_query = $db->query($query);






$query_select = "SELECT * FROM result";
$query_select_sql = $db->query($query_select);
$num = mysqli_num_rows($query_select_sql);


}

    if ($num > 0) {
    
        $sum_Sql = "SELECT SUM(result) as results FROM result";
        $sum = $db->query($sum_Sql);
       $re = null;
    
        if ($row = mysqli_fetch_assoc($sum)) {
            $re = $row['results'];
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>



    <script
  defer
  src="https://unpkg.com/alpinejs-money@latest/dist/money.min.js"
></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body

x-data="{ 
  
  title: 'Default Toast Notification', 
  description: '',
  type: 'default',
  position: 'top-center',
  expanded: false,
  popToast (custom){
      let html = '';
      if(typeof custom != 'undefined'){
          html = custom;
      }
      toast(this.title, { description: this.description, type: this.type, position: this.position, html: html })
  }
}" 
x-init="
  window.toast = function(message, options = {}){
      let description = '';
      let type = 'default';
      let position = 'top-center';
      let html = '';
      if(typeof options.description != 'undefined') description = options.description;
      if(typeof options.type != 'undefined') type = options.type;
      if(typeof options.position != 'undefined') position = options.position;
      if(typeof options.html != 'undefined') html = options.html;
      
      window.dispatchEvent(new CustomEvent('toast-show', { detail : { type: type, message: message, description: description, position : position, html: html }}));
  }

  window.customToastHTML = `
      <div class='relative flex items-start justify-center p-4'>
          <img src='https://cdn.devdojo.com/images/august2023/headshot-new.jpeg' class='w-10 h-10 mr-2 rounded-full'>
          <div class='flex flex-col'>
              <p class='text-sm font-medium text-gray-800'>New Friend Request</p>
              <p class='mt-1 text-xs leading-none text-gray-800'>Friend request from John Doe.</p>
              <div class='flex mt-3'>
                  <button type='button' @click='burnToast(toast.id)' class='inline-flex items-center px-2 py-1 text-xs font-semibold text-white bg-indigo-600 rounded shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600'>Accept</button>
                  <button type='button' @click='burnToast(toast.id)' class='inline-flex items-center px-2 py-1 ml-3 text-xs font-semibold text-gray-900 bg-white rounded shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50'>Decline</button>
              </div>
          </div>
      </div>
  `
"



>



<template x-teleport="body">
        <ul 
            x-data="{ 
                toasts: [],
                toastsHovered: false,
                expanded: false,
                layout: 'default',
                position: 'top-center',
                paddingBetweenToasts: 15,
                deleteToastWithId (id){
                    for(let i = 0; i < this.toasts.length; i++){
                        if(this.toasts[i].id === id){
                            this.toasts.splice(i, 1);
                            break;
                        }
                    }
                },
                burnToast(id){
                    burnToast = this.getToastWithId(id);
                    burnToastElement = document.getElementById(burnToast.id);
                    if(burnToastElement){
                        if(this.toasts.length == 1){
                            if(this.layout=='default'){
                                this.expanded = false;
                            }
                            burnToastElement.classList.remove('translate-y-0');
                            if(this.position.includes('bottom')){
                                burnToastElement.classList.add('translate-y-full');
                            } else {
                                burnToastElement.classList.add('-translate-y-full');
                            }
                            burnToastElement.classList.add('-translate-y-full');
                        }
                        burnToastElement.classList.add('opacity-0');
                        let that = this;
                        setTimeout(function(){
                            that.deleteToastWithId(id);
                            setTimeout(function(){
                                that.stackToasts();
                            }, 1)
                        }, 300);
                    }
                },
                getToastWithId(id){
                    for(let i = 0; i < this.toasts.length; i++){
                        if(this.toasts[i].id === id){
                            return this.toasts[i];
                        }
                    }
                },
                stackToasts(){
                    this.positionToasts();
                    this.calculateHeightOfToastsContainer();
                    let that = this;
                    setTimeout(function(){
                        that.calculateHeightOfToastsContainer();
                    }, 300);
                },
                positionToasts(){
                    if(this.toasts.length == 0) return;
                    let topToast = document.getElementById( this.toasts[0].id );
                    topToast.style.zIndex = 100;
                    if(this.expanded){
                        if(this.position.includes('bottom')){
                            topToast.style.top = 'auto';
                            topToast.style.bottom = '0px';
                        } else {
                            topToast.style.top = '0px';
                        }
                    }

                    let bottomPositionOfFirstToast = this.getBottomPositionOfElement(topToast);

                    if(this.toasts.length == 1) return;
                    let middleToast = document.getElementById( this.toasts[1].id );
                    middleToast.style.zIndex = 90;

                    if(this.expanded){
                        middleToastPosition = topToast.getBoundingClientRect().height +
                                                this.paddingBetweenToasts + 'px';

                        if(this.position.includes('bottom')){
                            middleToast.style.top = 'auto';
                            middleToast.style.bottom = middleToastPosition;
                        } else {
                            middleToast.style.top = middleToastPosition;
                        }

                        middleToast.style.scale = '100%';
                        middleToast.style.transform = 'translateY(0px)';
                        
                    } else {
                        middleToast.style.scale = '94%';
                        if(this.position.includes('bottom')){
                            middleToast.style.transform = 'translateY(-16px)';
                        } else {
                            this.alignBottom(topToast, middleToast);
                            middleToast.style.transform = 'translateY(16px)';
                        }
                    }
                    

                    if(this.toasts.length == 2) return;
                    let bottomToast = document.getElementById( this.toasts[2].id );
                    bottomToast.style.zIndex = 80;
                    if(this.expanded){
                        bottomToastPosition = topToast.getBoundingClientRect().height + 
                                                this.paddingBetweenToasts + 
                                                middleToast.getBoundingClientRect().height +
                                                this.paddingBetweenToasts + 'px';
                        
                        if(this.position.includes('bottom')){
                            bottomToast.style.top = 'auto';
                            bottomToast.style.bottom = bottomToastPosition;
                        } else {
                            bottomToast.style.top = bottomToastPosition;
                        }

                        bottomToast.style.scale = '100%';
                        bottomToast.style.transform = 'translateY(0px)';
                    } else {
                        bottomToast.style.scale = '88%';
                        if(this.position.includes('bottom')){
                            bottomToast.style.transform = 'translateY(-32px)';
                        } else {
                            this.alignBottom(topToast, bottomToast);
                            bottomToast.style.transform = 'translateY(32px)';
                        }
                    }

                    

                    if(this.toasts.length == 3) return;
                    let burnToast = document.getElementById( this.toasts[3].id );
                    burnToast.style.zIndex = 70;
                    if(this.expanded){
                        burnToastPosition = topToast.getBoundingClientRect().height + 
                                                this.paddingBetweenToasts + 
                                                middleToast.getBoundingClientRect().height + 
                                                this.paddingBetweenToasts + 
                                                bottomToast.getBoundingClientRect().height + 
                                                this.paddingBetweenToasts + 'px';
                        
                        if(this.position.includes('bottom')){
                            burnToast.style.top = 'auto';
                            burnToast.style.bottom = burnToastPosition;
                        } else {
                            burnToast.style.top = burnToastPosition;
                        }

                        burnToast.style.scale = '100%';
                        burnToast.style.transform = 'translateY(0px)';
                    } else {
                        burnToast.style.scale = '82%';
                        this.alignBottom(topToast, burnToast);
                        burnToast.style.transform = 'translateY(48px)';
                    }

                    burnToast.firstElementChild.classList.remove('opacity-100');
                    burnToast.firstElementChild.classList.add('opacity-0');

                    let that = this;
                    // Burn 🔥 (remove) last toast
                    setTimeout(function(){
                            that.toasts.pop();
                        }, 300);

                    if(this.position.includes('bottom')){
                            middleToast.style.top = 'auto';
                    }

                    return;
                },
                alignBottom(element1, element2) {
                    // Get the top position and height of the first element
                    let top1 = element1.offsetTop;
                    let height1 = element1.offsetHeight;

                    // Get the height of the second element
                    let height2 = element2.offsetHeight;

                    // Calculate the top position for the second element
                    let top2 = top1 + (height1 - height2);

                    // Apply the calculated top position to the second element
                    element2.style.top = top2 + 'px';
                },
                alignTop(element1, element2) {
                    // Get the top position of the first element
                    let top1 = element1.offsetTop;

                    // Apply the same top position to the second element
                    element2.style.top = top1 + 'px';
                },
                resetBottom(){
                    for(let i = 0; i < this.toasts.length; i++){
                        if(document.getElementById( this.toasts[i].id )){
                            let toastElement = document.getElementById( this.toasts[i].id );
                            toastElement.style.bottom = '0px';
                        }
                    }
                },
                resetTop(){
                    for(let i = 0; i < this.toasts.length; i++){
                        if(document.getElementById( this.toasts[i].id )){
                            let toastElement = document.getElementById( this.toasts[i].id );
                            toastElement.style.top = '0px';
                        }
                    }
                },
                getBottomPositionOfElement(el){
                    return (el.getBoundingClientRect().height + el.getBoundingClientRect().top);
                },
                calculateHeightOfToastsContainer(){
                    if(this.toasts.length == 0){
                        $el.style.height = '0px';
                        return;
                    }

                    lastToast = this.toasts[this.toasts.length - 1];
                    lastToastRectangle = document.getElementById(lastToast.id).getBoundingClientRect();
                    
                    firstToast = this.toasts[0];
                    firstToastRectangle = document.getElementById(firstToast.id).getBoundingClientRect();

                    if(this.toastsHovered){
                        if(this.position.includes('bottom')){
                            $el.style.height = ((firstToastRectangle.top + firstToastRectangle.height) - lastToastRectangle.top) + 'px';
                        } else {
                            $el.style.height = ((lastToastRectangle.top + lastToastRectangle.height) - firstToastRectangle.top) + 'px';
                        }
                    } else {
                        $el.style.height = firstToastRectangle.height + 'px';
                    }
                }
            }"
            @set-toasts-layout.window="
                layout=event.detail.layout;
                if(layout == 'expanded'){
                    expanded=true;
                } else {
                    expanded=false;
                }
                stackToasts();
            "
            @toast-show.window="
                event.stopPropagation();
                if(event.detail.position){
                    position = event.detail.position;
                }
                toasts.unshift({
                    id: 'toast-' + Math.random().toString(16).slice(2),
                    show: false,
                    message: event.detail.message,
                    description: event.detail.description,
                    type: event.detail.type,
                    html: event.detail.html
                });
            "
            @mouseenter="toastsHovered=true;"
            @mouseleave="toastsHovered=false"
            x-init="
                if(layout == 'expanded'){
                    expanded = true;
                }
                stackToasts();
                $watch('toastsHovered', function(value){

                    if(layout == 'default'){
                        if(position.includes('bottom')){
                            resetBottom();
                        } else {
                            resetTop();
                        }

                        if(value){
                            // calculate the new positions
                            expanded = true;
                            if(layout == 'default'){
                                stackToasts();
                            }
                        } else {
                            if(layout == 'default'){
                                expanded = false;
                                //setTimeout(function(){
                                stackToasts();
                            //}, 10);
                                setTimeout(function(){
                                    stackToasts();
                                }, 10)
                            }
                        }
                    }
                });
            "
            class="fixed block w-full group z-[99] sm:max-w-xs"
            :class="{ 'right-0 top-0 sm:mt-6 sm:mr-6': position=='top-right', 'left-0 top-0 sm:mt-6 sm:ml-6': position=='top-left', 'left-1/2 -translate-x-1/2 top-0 sm:mt-6': position=='top-center', 'right-0 bottom-0 sm:mr-6 sm:mb-6': position=='bottom-right', 'left-0 bottom-0 sm:ml-6 sm:mb-6': position=='bottom-left', 'left-1/2 -translate-x-1/2 bottom-0 sm:mb-6': position=='bottom-center' }"
            x-cloak>
        
            <template x-for="(toast, index) in toasts" :key="toast.id">
                <li
                    :id="toast.id"
                    x-data="{
                        toastHovered: false
                    }"
                    x-init="
                        
                        if(position.includes('bottom')){
                            $el.firstElementChild.classList.add('toast-bottom');
                            $el.firstElementChild.classList.add('opacity-0', 'translate-y-full');
                        } else {
                            $el.firstElementChild.classList.add('opacity-0', '-translate-y-full');
                        }
                        setTimeout(function(){
                            
                            setTimeout(function(){
                                if(position.includes('bottom')){
                                    $el.firstElementChild.classList.remove('opacity-0', 'translate-y-full');
                                } else {
                                    $el.firstElementChild.classList.remove('opacity-0', '-translate-y-full');
                                }
                                $el.firstElementChild.classList.add('opacity-100', 'translate-y-0');

                                setTimeout(function(){
                                    stackToasts();
                                }, 10);
                            }, 5);
                        }, 50);
        
                        setTimeout(function(){
                            setTimeout(function(){
                                $el.firstElementChild.classList.remove('opacity-100');
                                $el.firstElementChild.classList.add('opacity-0');
                                if(toasts.length == 1){
                                    $el.firstElementChild.classList.remove('translate-y-0');
                                    $el.firstElementChild.classList.add('-translate-y-full');
                                }
                                setTimeout(function(){
                                    deleteToastWithId(toast.id)
                                }, 300);
                            }, 5);
                        }, 4000); 
                    "
                    @mouseover="toastHovered=true"
                    @mouseout="toastHovered=false"
                    class="absolute w-full duration-300 ease-out select-none sm:max-w-xs"
                    :class="{ 'toast-no-description': !toast.description }"
                    >
                    <span 
                        class="relative flex flex-col items-start shadow-[0_5px_15px_-3px_rgb(0_0_0_/_0.08)] w-full transition-all duration-300 ease-out bg-white border border-gray-100 sm:rounded-md sm:max-w-xs group"
                        :class="{ 'p-4' : !toast.html, 'p-0' : toast.html }"
                    >
                        <template x-if="!toast.html">
                            <div class="relative">
                                <div class="flex items-center"
                                    :class="{ 'text-green-500' : toast.type=='success', 'text-blue-500' : toast.type=='info', 'text-orange-400' : toast.type=='warning', 'text-red-500' : toast.type=='danger', 'text-gray-800' : toast.type=='default' }">
                                    
                                    <svg x-show="toast.type=='success'" class="w-[18px] h-[18px] mr-1.5 -ml-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2ZM16.7744 9.63269C17.1238 9.20501 17.0604 8.57503 16.6327 8.22559C16.2051 7.87615 15.5751 7.93957 15.2256 8.36725L10.6321 13.9892L8.65936 12.2524C8.24484 11.8874 7.61295 11.9276 7.248 12.3421C6.88304 12.7566 6.92322 13.3885 7.33774 13.7535L9.31046 15.4903C10.1612 16.2393 11.4637 16.1324 12.1808 15.2547L16.7744 9.63269Z" fill="currentColor"></path></svg>
                                    <svg x-show="toast.type=='info'" class="w-[18px] h-[18px] mr-1.5 -ml-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2ZM12 9C12.5523 9 13 8.55228 13 8C13 7.44772 12.5523 7 12 7C11.4477 7 11 7.44772 11 8C11 8.55228 11.4477 9 12 9ZM13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12V16C11 16.5523 11.4477 17 12 17C12.5523 17 13 16.5523 13 16V12Z" fill="currentColor"></path></svg>
                                    <svg x-show="toast.type=='warning'" class="w-[18px] h-[18px] mr-1.5 -ml-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.44829 4.46472C10.5836 2.51208 13.4105 2.51168 14.5464 4.46401L21.5988 16.5855C22.7423 18.5509 21.3145 21 19.05 21L4.94967 21C2.68547 21 1.25762 18.5516 2.4004 16.5862L9.44829 4.46472ZM11.9995 8C12.5518 8 12.9995 8.44772 12.9995 9V13C12.9995 13.5523 12.5518 14 11.9995 14C11.4473 14 10.9995 13.5523 10.9995 13V9C10.9995 8.44772 11.4473 8 11.9995 8ZM12.0009 15.99C11.4486 15.9892 11.0003 16.4363 10.9995 16.9886L10.9995 16.9986C10.9987 17.5509 11.4458 17.9992 11.9981 18C12.5504 18.0008 12.9987 17.5537 12.9995 17.0014L12.9995 16.9914C13.0003 16.4391 12.5532 15.9908 12.0009 15.99Z" fill="currentColor"></path></svg>
                                    <svg x-show="toast.type=='danger'" class="w-[18px] h-[18px] mr-1.5 -ml-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12ZM11.9996 7C12.5519 7 12.9996 7.44772 12.9996 8V12C12.9996 12.5523 12.5519 13 11.9996 13C11.4474 13 10.9996 12.5523 10.9996 12V8C10.9996 7.44772 11.4474 7 11.9996 7ZM12.001 14.99C11.4488 14.9892 11.0004 15.4363 10.9997 15.9886L10.9996 15.9986C10.9989 16.5509 11.446 16.9992 11.9982 17C12.5505 17.0008 12.9989 16.5537 12.9996 16.0014L12.9996 15.9914C13.0004 15.4391 12.5533 14.9908 12.001 14.99Z" fill="currentColor"></path></svg>
                                    <p class="text-[13px] font-medium leading-none text-gray-800" x-text="toast.message"></p>
                                </div>
                                <p x-show="toast.description" 
                                    :class="{ 'pl-5' : toast.type!='default' }"
                                    class="mt-1.5 text-xs leading-none opacity-70" x-text="toast.description"></p>
                            </div>
                        </template>
                        <template x-if="toast.html">
                            <div x-html="toast.html"></div>
                        </template>
                        <span 
                            @click="burnToast(toast.id)"
                            class="absolute right-0 p-1.5 mr-2.5 text-gray-400 duration-100 ease-in-out rounded-full opacity-0 cursor-pointer hover:bg-gray-50 hover:text-gray-500"
                            :class="{ 'top-1/2 -translate-y-1/2' : !toast.description && !toast.html, 'top-0 mt-2.5' : (toast.description || toast.html), 'opacity-100' : toastHovered, 'opacity-0' : !toastHovered }"
                        >
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </span>
                    </span>
                </li>
            </template>
        </ul>
    </template>


      <section





   class="min-h-screen content-center relative "
      >


<div class="max-w-lg m-auto  ">


       <form 
       
        method="post"
        action="admin.php"
        x-data="{ 
        user: '',
    
        kategori: '',
    
    
         disabled() {
            return this.user.trim() !== ''  &&  this.kategori.trim() !== '' ;

    
            
        }
        
          
        }"    @submit="!disabled() && $event.preventDefault()"
       class=" space-y-10 ">
            
       <div class="flex w-full flex-col gap-1 ">
       <label for="user" class="block mb-1 text-sm font-medium text-neutral-500">Username</label>
      <input x-model="user" type="text" name="user" class="shadow placeholder:font-medium  border border-neutral-100  placeholder:text-sm  rounded-lg outline-none focus-none  p-3" placeholder="user" >
</div>

<div x-data="{
      datePickerOpen: false,
      datePickerValue: '',
      datePickerFormat: 'M d, Y',
      datePickerMonth: '',
      datePickerYear: '',
      datePickerDay: '',
      datePickerDaysInMonth: [],
      datePickerBlankDaysInMonth: [],
      datePickerMonthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
      datePickerDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
      datePickerDayClicked(day) {
        let selectedDate = new Date(this.datePickerYear, this.datePickerMonth, day);
        this.datePickerDay = day;
        this.datePickerValue = this.datePickerFormatDate(selectedDate);
        this.datePickerIsSelectedDate(day);
        this.datePickerOpen = false;
      },
      datePickerPreviousMonth(){
        if (this.datePickerMonth == 0) { 
            this.datePickerYear--; 
            this.datePickerMonth = 12; 
        } 
        this.datePickerMonth--;
        this.datePickerCalculateDays();
      },
      datePickerNextMonth(){
        if (this.datePickerMonth == 11) { 
            this.datePickerMonth = 0; 
            this.datePickerYear++; 
        } else { 
            this.datePickerMonth++; 
        }
        this.datePickerCalculateDays();
      },
      datePickerIsSelectedDate(day) {
        const d = new Date(this.datePickerYear, this.datePickerMonth, day);
        return this.datePickerValue === this.datePickerFormatDate(d) ? true : false;
      },
      datePickerIsToday(day) {
        const today = new Date();
        const d = new Date(this.datePickerYear, this.datePickerMonth, day);
        return today.toDateString() === d.toDateString() ? true : false;
      },
      datePickerCalculateDays() {
        let daysInMonth = new Date(this.datePickerYear, this.datePickerMonth + 1, 0).getDate();
        // find where to start calendar day of week
        let dayOfWeek = new Date(this.datePickerYear, this.datePickerMonth).getDay();
        let blankdaysArray = [];
        for (var i = 1; i <= dayOfWeek; i++) {
            blankdaysArray.push(i);
        }
        let daysArray = [];
        for (var i = 1; i <= daysInMonth; i++) {
            daysArray.push(i);
        }
        this.datePickerBlankDaysInMonth = blankdaysArray;
        this.datePickerDaysInMonth = daysArray;
      },
      datePickerFormatDate(date) {
        let formattedDay = this.datePickerDays[date.getDay()];
        let formattedDate = ('0' + date.getDate()).slice(-2); // appends 0 (zero) in single digit date
        let formattedMonth = this.datePickerMonthNames[date.getMonth()];
        let formattedMonthShortName = this.datePickerMonthNames[date.getMonth()].substring(0, 3);
        let formattedMonthInNumber = ('0' + (parseInt(date.getMonth()) + 1)).slice(-2);
        let formattedYear = date.getFullYear();

        if (this.datePickerFormat === 'M d, Y') {
          return `${formattedMonthShortName} ${formattedDate}, ${formattedYear}`;
        }
        if (this.datePickerFormat === 'MM-DD-YYYY') {
          return `${formattedMonthInNumber}-${formattedDate}-${formattedYear}`;
        }
        if (this.datePickerFormat === 'DD-MM-YYYY') {
          return `${formattedDate}-${formattedMonthInNumber}-${formattedYear}`;
        }
        if (this.datePickerFormat === 'YYYY-MM-DD') {
          return `${formattedYear}-${formattedMonthInNumber}-${formattedDate}`;
        }
        if (this.datePickerFormat === 'D d M, Y') {
          return `${formattedDay} ${formattedDate} ${formattedMonthShortName} ${formattedYear}`;
        }
        
        return `${formattedMonth} ${formattedDate}, ${formattedYear}`;
      },
    }" x-init="
        currentDate = new Date();
        if (datePickerValue) {
            currentDate = new Date(Date.parse(datePickerValue));
        }
        datePickerMonth = currentDate.getMonth();
        datePickerYear = currentDate.getFullYear();
        datePickerDay = currentDate.getDay();
        datePickerValue = datePickerFormatDate( currentDate );
        datePickerCalculateDays();
    " x-cloak>
    <div class="container   mx-auto ">
        <div class="w-full mb-5">
            <label for="datepicker" class="block mb-1 text-sm font-medium text-neutral-500">Select Date</label>
            <div class="relative w-full">
                <input x-ref="datePickerInput"  type="text" @click="datePickerOpen=!datePickerOpen" x-model="datePickerValue" x-on:keydown.escape="datePickerOpen=false" class="flex w-full h-10 px-3 py-2 text-sm bg-white border rounded-md text-neutral-600 border-neutral-300 ring-offset-background placeholder:text-neutral-400 focus:border-neutral-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neutral-400 disabled:cursor-not-allowed disabled:opacity-50" name="date" placeholder="Select date" readonly />
                <div @click="datePickerOpen=!datePickerOpen; if(datePickerOpen){ $refs.datePickerInput.focus() }" class="absolute top-0 right-0 px-3 py-2 cursor-pointer text-neutral-400 hover:text-neutral-500">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div  
                    x-show="datePickerOpen"
                    x-transition
                    @click.away="datePickerOpen = false" 
                    class="absolute top-0 left-0 max-w-lg p-4 mt-12 antialiased bg-white z-50 border rounded-lg shadow w-[17rem] border-neutral-200/70">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <span x-text="datePickerMonthNames[datePickerMonth]" class="text-lg font-bold text-gray-800"></span>
                            <span x-text="datePickerYear" class="ml-1 text-lg font-normal text-gray-600"></span>
                        </div>
                        <div>
                            <button @click="datePickerPreviousMonth()" type="button" class="inline-flex p-1 transition duration-100 ease-in-out rounded-full cursor-pointer focus:outline-none focus:shadow-outline hover:bg-gray-100">
                                <svg class="inline-flex w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                            </button>
                            <button @click="datePickerNextMonth()" type="button" class="inline-flex p-1 transition duration-100 ease-in-out rounded-full cursor-pointer focus:outline-none focus:shadow-outline hover:bg-gray-100">
                                <svg class="inline-flex w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-7 mb-3">
                        <template x-for="(day, index) in datePickerDays" :key="index">
                            <div class="px-0.5">
                                <div x-text="day" class="text-xs font-medium text-center text-gray-800"></div>
                            </div>
                        </template>
                    </div>
                    <div class="grid grid-cols-7">
                        <template x-for="blankDay in datePickerBlankDaysInMonth">
                            <div class="p-1 text-sm text-center border border-transparent"></div>
                        </template>
                        <template x-for="(day, dayIndex) in datePickerDaysInMonth" :key="dayIndex">
                            <div class="px-0.5 mb-1 aspect-square">
                                <div 
                                    x-text="day"
                                    @click="datePickerDayClicked(day)" 
                                    :class="{
                                        'bg-neutral-200': datePickerIsToday(day) == true, 
                                        'text-gray-600 hover:bg-neutral-200': datePickerIsToday(day) == false && datePickerIsSelectedDate(day) == false,
                                        'bg-neutral-800 text-white hover:bg-opacity-75': datePickerIsSelectedDate(day) == true
                                    }" 
                                    class="flex items-center justify-center text-sm leading-none text-center rounded-full cursor-pointer h-7 w-7"></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div  class="space-y-5">
    <label for="radio-group" class="block  text-sm font-medium text-neutral-500">Category Ticket</label>
    <div class="space-y-3">

        <label class="flex items-start p-5 space-x-3 bg-white border rounded-md shadow-sm hover:bg-gray-50 border-neutral-200/70">
            <input x-model="kategori" value="domestik" type="radio" name="ketegori"  class="text-gray-900 accent-black translate-y-px focus:ring-gray-700" />
            <span class="relative flex flex-col text-left space-y-1.5 leading-none">
                <span  class="font-semibold">Domestik</span>
                <span class="text-sm opacity-50 line-clamp-2">Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae saepe, dicta tempore ab expedita corrupti quod facere quo odit autem deleniti tenetur consequuntur, quisquam adipisci eum ipsam quis dolore aut?</span>
            </span>
        </label>
        <label class="flex items-start p-5 space-x-3 bg-white border rounded-md shadow-sm hover:bg-gray-50 border-neutral-200/70">
            <input x-model="kategori" value="internasional" type="radio" name="ketegori"  class="text-gray-900 accent-black translate-y-px focus:ring-gray-700" />
            <span class="relative flex flex-col text-left space-y-1.5 leading-none">
                <span  class="font-semibold">International</span>
                <span class="text-sm opacity-50 line-clamp-2">Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae saepe, dicta tempore ab expedita corrupti quod facere quo odit autem deleniti tenetur consequuntur, quisquam adipisci eum ipsam quis dolore aut?</span>
            </span>
        </label>
    </div>

</div>

<div x-data="{ currentVal: 1, minVal: 0, maxVal: 100, decimalPoints: 0, incrementAmount: 1 }" class="flex flex-col gap-1">
<label for="user" class="block mb-1 text-sm font-medium text-neutral-500">Tickect</label>
    <div  class="flex items-center">
        <button x-on:click="currentVal = Math.max(minVal, currentVal - incrementAmount)" type="button" class="flex h-10 items-center justify-center rounded-l-sm border border-neutral-300 bg-neutral-50 px-4 py-2 text-neutral-600 hover:opacity-75 focus-visible:z-10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black active:opacity-100 active:outline-offset-0 " aria-label="subtract">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor" fill="none" stroke-width="2" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/>
            </svg>
        </button>
        <input x-model="currentVal.toFixed(decimalPoints)" id="counterInput" name="tiket" type="text" class="border-x-none h-10 w-20 rounded-none border-y border-neutral-300 bg-neutral-50/50 text-center text-neutral-900 " readonly />
        <button x-on:click="currentVal = Math.min(maxVal, currentVal + incrementAmount)" type="button"  class="flex h-10 items-center justify-center rounded-r-sm border border-neutral-300 bg-neutral-50 px-4 py-2 text-neutral-600 hover:opacity-75 focus-visible:z-10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black active:opacity-100 active:outline-offset-0 " aria-label="add">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor" fill="none" stroke-width="2" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
        </button>
    </div>
</div>


<button @click="title='Pls Input The user and pick the category'; type='danger';  !disabled() ? popToast() : ''" type="submit" name="submit" class="w-full cursor-pointer bg-neutral-50 font-medium rounded-lg hover:bg-neutral-100 h-10 border border-gray-200">Submit</button>
                                </form>
                                <?php if($num > 0) { ?>
                                <a href="#data"  class="absolute bg-neutral-100 transform  left-50 shadow border-gray-100  bottom-10 border rounded-full p-5 animate-bounce">
                                <svg 
                                
                               
                                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M18.0613 15L12 21.0613L5.93866 15" stroke="black" stroke-width="null" stroke-linecap="round" stroke-linejoin="round" class="my-path"></path>
<path d="M12 3.06152L12 21.0615" stroke="black" stroke-width="null" stroke-linecap="round" class="my-path"></path>
</svg>
                                </a>
                                <?php } ?>
                                </div>

           


      </section>


      <?php if($num > 0) { ?>
    

<section id="data" class="min-h-screen  py-30  content-center"> 






<div class="relative overflow-x-auto shadow-md   max-w-7xl m-auto sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Name
                </th>
                <th scope="col" class="px-6 py-3">
                    Category
                </th>
                <th scope="col" class="px-6 py-3">
                    Day 
                </th>
                <th scope="col" class="px-6 py-3">
                    Date
                </th>
                <th scope="col" class="px-6 py-3">
                    Ticket
                </th>
                <th scope="col" class="px-6 py-3">
                    Price
                </th>
                <th scope="col" class="px-6 py-3">
                    Diskon
                </th>
                <th scope="col" class="px-6 py-3">
                    Result
                </th>
                <th scope="col" class="px-6 py-3">
                    Edir
                </th>
            </tr>
        </thead>
        <tbody>
        <?php
        while( $r = mysqli_fetch_array($query_select_sql)) {

       
        
        ?>
            <tr class="odd:bg-white  even:bg-gray-50 border-b  border-gray-200">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                    <?=$r["user"] ?>
                </th>
                <td class="px-6 py-4">
                <?=$r["kategori"] ?>
                </td>
                <td class="px-6 py-4">
                <?=$r["weekend"] ?>
                </td>
                <td class="px-6 py-4">
                 <?=$r["tanggal"] ?>
                </td>
                <td class="px-6 py-4"
                
               
                
                >
         <?=$r["ticket"] ?>
                </td>
                <td class="px-6 py-4">
                   <span  x-money.id-ID.IDR ="<?=$r["price"] ?>"> </span>
                </td>
                <td class="px-6 py-4" 
                
                
                >
           <?=$r["diskon"] ?>
                </td>
                <td class="px-6 py-4">
              <span 
             x-money.id-ID.IDR ="<?=$r["result"] ?>"
            
            ></span>
                </td>
                <td class="px-6 py-4">
                    <a href="#" class="font-medium text-blue-600  hover:underline">Edit</a>
                </td>
               
            </tr>
           <?php  } ?>
        </tbody>
    </table>
</div>
<div class="w-full flex justify-between m-auto max-w-7xl border-t border-gray-300 py-6 mt-14">
        <h1 class="font-semibold">TOTAL :</h1> 
        <h1 class="font-semibold"><span 
        
        x-money.id-ID.IDR ="<?=$re?>">
        ></span></h1> 
</div>






</section>
<?php } ?>
</body>
</html>