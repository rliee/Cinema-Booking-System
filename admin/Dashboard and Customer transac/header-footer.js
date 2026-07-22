const menuBtn=document.getElementById("menuBtn");
const nav=document.getElementById("nav");

menuBtn.onclick=()=>{
    menuBtn.classList.toggle("active");
    nav.classList.toggle("active");
}

// Sticky Shadow

const header=document.getElementById("header");

window.addEventListener("scroll",()=>{

header.classList.toggle("scrolled",window.scrollY>30);

let scrollTop=document.documentElement.scrollTop;
let height=document.documentElement.scrollHeight-document.documentElement.clientHeight;
let progress=(scrollTop/height)*100;

document.querySelector(".progress-bar").style.width=progress+"%";

});

// Close menu after clicking a link

document.querySelectorAll(".nav-links a").forEach(link=>{

link.addEventListener("click",()=>{

menuBtn.classList.remove("active");
nav.classList.remove("active");

});

});

// Logo animation

document.querySelector(".logo").addEventListener("mouseenter",function(){
this.style.transform="rotate(-2deg) scale(1.08)";
});

document.querySelector(".logo").addEventListener("mouseleave",function(){
this.style.transform="rotate(0deg) scale(1)";
});
