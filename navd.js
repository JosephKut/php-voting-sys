const Pre=document.getElementById('Pre');
const VPre=document.getElementById('VPre');
const GS=document.getElementById('GS');
const WC=document.getElementById('WC');
const FS=document.getElementById('FS');
const PO=document.getElementById('PO');
const PC=document.getElementById('PC');
const noticeB=document.getElementById('noticeB');
const ps=document.getElementById('post');

function note(){
    noticeB.style.display="flex";
    Pre.style.display="none";
    VPre.style.display="none";
    ps.style.display="none";
    GS.style.display="none";
    WC.style.display="none";
    FS.style.display="none";
    PO.style.display="none";
    PC.style.display="none";
}

document.getElementById('FPre').addEventListener('click', function(){
    Pre.style.display="flex";
    VPre.style.display="none";
    GS.style.display="none";
    WC.style.display="none";
    FS.style.display="none";
    PO.style.display="none";
    PC.style.display="none";
})

document.getElementById('FVPre').addEventListener('click', function(){
    Pre.style.display="none";
    VPre.style.display="flex";
    GS.style.display="none";
    WC.style.display="none";
    FS.style.display="none";
    PO.style.display="none";
    PC.style.display="none";
})

document.getElementById('FGS').addEventListener('click', function(){
    Pre.style.display="none";
    VPre.style.display="none";
    GS.style.display="flex";
    WC.style.display="none";
    FS.style.display="none";
    PO.style.display="none";
    PC.style.display="none";
})

document.getElementById('FFS').addEventListener('click', function(){
    Pre.style.display="none";
    VPre.style.display="none";
    GS.style.display="none";
    WC.style.display="none";
    FS.style.display="flex";
    PO.style.display="none";
    PC.style.display="none";
})

document.getElementById('FPO').addEventListener('click', function(){
    Pre.style.display="none";
    VPre.style.display="none";
    GS.style.display="none";
    WC.style.display="none";
    FS.style.display="none";
    PO.style.display="flex";
    PC.style.display="none";
})

document.getElementById('FPC').addEventListener('click', function(){
    Pre.style.display="none";
    VPre.style.display="none";
    GS.style.display="none";
    WC.style.display="none";
    FS.style.display="none";
    PO.style.display="none";
    PC.style.display="flex";
})

document.getElementById('FWC').addEventListener('click', function(){
    Pre.style.display="none";
    VPre.style.display="none";
    GS.style.display="none";
    WC.style.display="flex";
    FS.style.display="none";
    PO.style.display="none";
    PC.style.display="none";
})