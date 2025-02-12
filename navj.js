const Pre=document.getElementById('Pre');
const GS=document.getElementById('GS');
const WCom=document.getElementById('WCom');
const FS=document.getElementById('FS');
const MP=document.getElementById('MP');
const EP=document.getElementById('EP');
const noticeB=document.getElementById('noticeB');
const ps=document.getElementById('post');

function note(){
    noticeB.style.display="flex";
    Pre.style.display="none";
    ps.style.display="none";
    GS.style.display="none";
    WCom.style.display="none";
    FS.style.display="none";
    MP.style.display="none";
    EP.style.display="none";
}

document.getElementById('jp').addEventListener('click', function(){
    Pre.style.display="flex";
    GS.style.display="none";
    WCom.style.display="none";
    FS.style.display="none";
    MP.style.display="none";
    EP.style.display="none";
})

document.getElementById('jgs').addEventListener('click', function(){
    Pre.style.display="none";
    GS.style.display="flex";
    WCom.style.display="none";
    FS.style.display="none";
    MP.style.display="none";
    EP.style.display="none";
})

document.getElementById('jfs').addEventListener('click', function(){
    Pre.style.display="none";
    GS.style.display="none";
    WCom.style.display="none";
    FS.style.display="flex";
    MP.style.display="none";
    EP.style.display="none";
})

document.getElementById('jmp').addEventListener('click', function(){
    Pre.style.display="none";
    GS.style.display="none";
    WCom.style.display="none";
    FS.style.display="none";
    MP.style.display="flex";
    EP.style.display="none";
})

document.getElementById('jwc').addEventListener('click', function(){
    Pre.style.display="none";
    GS.style.display="none";
    WCom.style.display="flex";
    FS.style.display="none";
    MP.style.display="none";
    EP.style.display="none";
})

document.getElementById('jep').addEventListener('click', function(){
    Pre.style.display="none";
    GS.style.display="none";
    WCom.style.display="none";
    FS.style.display="none";
    MP.style.display="none";
    EP.style.display="flex";
})