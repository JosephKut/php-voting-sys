// const noticeB=document.getElementById('noticeB');
const ps=document.getElementById('post');
const content=document.getElementsByClassName("pv");

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