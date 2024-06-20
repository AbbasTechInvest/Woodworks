window.addEventListener("load",function(){
    for(var e=document.querySelectorAll("ul.nav-tabs > li"),t=0;t<e.length;t++)
    e[t].addEventListener("click",function(e){
        if(classList!=null){
        e.preventDefault(),
        document.querySelector("ul.nav-tabs li.active").classList.remove("active"),
        document.querySelector(".tab-pane.active").classList.remove("active");
        var t=e.currentTarget,r=e.target.getAttribute("href");
        t.classList.add("active"),
        document.querySelector(r).classList.add("active")
        }
    })
    
});


