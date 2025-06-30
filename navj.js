// const noticeB=document.getElementById('noticeB');
const ps=document.getElementById('post');
const content=document.getElementsByClassName("pv");

function view_summary(display){
    if (display == 'flex'){
    for (let i = 0; i<content.length; i++){
        content[i].style.display="none";
    }
    document.getElementById("view-summary").style.display = display;
    }else{
        document.getElementById("view-summary").style.display = display;
        content[0].style.display="flex";
    }
}

// function note(){
//     noticeB.style.display="flex";
//     Pre.style.display="none";
//     ps.style.display="none";
//     GS.style.display="none";
//     WCom.style.display="none";
//     FS.style.display="none";
//     MP.style.display="none";
//     EP.style.display="none";
// }
for (let n = 0; n<content.length; n++){
    document.getElementById(postButtonID[n]).addEventListener('click', function(){
        for (let i = 0; i<content.length; i++){
            content[i].style.display="none";
        }
        document.getElementById(postContentID[n]).style.display="flex";
    })
}