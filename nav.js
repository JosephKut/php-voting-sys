const notice=document.getElementById('info');
const noticeB=document.getElementById('noticeB');
const ps=document.getElementById('post');
const voted=document.getElementById('voted');
const session=document.getElementById('session');
const link=document.getElementById('link');

// const empty=document.getElementById('no_Can');

const FS=document.getElementById('FS');
const MP=document.getElementById('MP');
const EP=document.getElementById('EP');

function note(){
    voted.style.display="none";
    ps.style.display="none";
    noticeB.style.display="flex";
    Pre.style.display="none";
    GS.style.display="none";
    NUGSP.style.display="none";
    WCom.style.display="none";
}

function checked(){
    ps.style.display="none";
    Pre.style.display="none";
}

// const supB=document.getElementById('regB');
// const sreB=document.getElementById('retB');
// const sinB=document.getElementById('logB');
// const supF=document.getElementById('reg-flex');
// const sreF=document.getElementById('ret-flex');
// const sinF=document.getElementById('log-flex');

// supB.addEventListener(click, function(){
//     supF.style.display="block";
//     sreF.style.display="none";
//     sinF.style.display="none";
// })

// sreB.addEventListener(click, function(){
//     supF.style.display="none";
//     sreF.style.display="block";
//     sinF.style.display="none";
// })

// sinB.addEventListener(click, function(){
//     supF.style.display="none";
//     sreF.style.display="none";
//     sinF.style.display="block";
// })

// function navi(){
//     supF.style.display="none";
//     sreF.style.display="none";
//     sinF.style.display="flex";
// }

// function navr(){
//     supF.style.display="none";
//     sreF.style.display="flex";
//     sinF.style.display="none";
// }

// function navu(){
//     supF.style.display="flex";
//     sreF.style.display="none";
//     sinF.style.display="none";
// }

const dash=document.getElementById('dash');
const postM=document.getElementById('postM');
const candidateM=document.getElementById('candidateM');
//const deptM=document.getElementById('deptM');
const viewC=document.getElementById('viewC');
const viewR=document.getElementById('viewR');
//const NB=document.getElementById('NB');
const ST=document.getElementById('ST');

function Fdash(){
    dash.style.display="flex";
    postM.style.display="none";
    candidateM.style.display="none";
    viewC.style.display="none";
    viewR.style.display="none";
    ST.style.display="none";
}

function FpostM(){
    dash.style.display="none";
    postM.style.display="flex";
    candidateM.style.display="none";
    viewC.style.display="none";
    viewR.style.display="none";
    ST.style.display="none";
}

function FcandidateM(){
    dash.style.display="none";
    postM.style.display="none";
    candidateM.style.display="flex";
    viewC.style.display="none";
    viewR.style.display="none";
    ST.style.display="none";
}

// function FdeptM(){
//     dash.style.display="none";
//     postM.style.display="none";
//     candidateM.style.display="none";
//     viewC.style.display="none";
//     viewR.style.display="none";
//     NB.style.display="none"
//     ST.style.display="none";
//     deptM.style.display="flex";
// }

function FviewC(){
    dash.style.display="none";
    postM.style.display="none";
    candidateM.style.display="none";
    viewC.style.display="flex";
    viewR.style.display="none";
    ST.style.display="none";
}

function FviewR(){
    dash.style.display="none";
    postM.style.display="none";
    candidateM.style.display="none";
    viewC.style.display="none";
    viewR.style.display="flex";
    ST.style.display="none";
}

// function FNB(){
//     dash.style.display="none";
//     postM.style.display="none";
//     candidateM.style.display="none";
//     viewC.style.display="none";
//     viewR.style.display="none";
//     NB.style.display="flex";
//     ST.style.display="none";
//     deptM.style.display="none";
// }

function FST(){
    dash.style.display="none";
    postM.style.display="none";
    candidateM.style.display="none";
    viewC.style.display="none";
    viewR.style.display="none";
    ST.style.display="flex";
}

// const Pre=document.getElementById('Pre');
// const GS=document.getElementById('GS');
// const NUGSP=document.getElementById('NUGSP');
// const WCom=document.getElementById('WCom');
// const Tre=document.getElementById('Tre');
// const NUGSS=document.getElementById('NUGSS');
// const NUGST=document.getElementById('NUGST');
const content=document.getElementsByClassName("pv");

for (let n = 0; n<content.length; n++){
    document.getElementById(postButtonID[n]).addEventListener('click', function(){
        for (let i = 0; i<content.length; i++){
            content[i].style.display="none";
        }
        document.getElementById(postContentID[n]).style.display="flex";
    })
}

// function FPre(){
//     Pre.style.display="flex";
//     GS.style.display="none";
//     NUGSP.style.display="none";
//     WCom.style.display="none";
//     Tre.style.display="none";
//     NUGSS.style.display="none";
//     NUGST.style.display="none";
// }

// function FGS(){
//     Pre.style.display="none";
//     GS.style.display="flex";
//     NUGSP.style.display="none";
//     WCom.style.display="none";
//     Tre.style.display="none";
//     NUGSS.style.display="none";
//     NUGST.style.display="none";
// }

// function FNUGSP(){
//     Pre.style.display="none";
//     GS.style.display="none";
//     NUGSP.style.display="flex";
//     WCom.style.display="none";
//     Tre.style.display="none";
//     NUGSS.style.display="none";
//     NUGST.style.display="none";
// }

// function FWCom(){
//     Pre.style.display="none";
//     GS.style.display="none";
//     NUGSP.style.display="none";
//     WCom.style.display="flex";
//     Tre.style.display="none";
//     NUGSS.style.display="none";
//     NUGST.style.display="none";
// }

// document.getElementById('FTre').addEventListener('click', function(){
//     Pre.style.display="none";
//     GS.style.display="none";
//     NUGSP.style.display="none";
//     WCom.style.display="none";
//     Tre.style.display="flex";
//     NUGSS.style.display="none";
//     NUGST.style.display="none";
// })

// document.getElementById('NUGS_S').addEventListener('click', function(){
//     Pre.style.display="none";
//     GS.style.display="none";
//     NUGSP.style.display="none";
//     WCom.style.display="none";
//     Tre.style.display="none";
//     NUGSS.style.display="flex";
//     NUGST.style.display="none";
// })

// document.getElementById('NUGS_T').addEventListener('click', function(){
//     Pre.style.display="none";
//     GS.style.display="none";
//     NUGSP.style.display="none";
//     WCom.style.display="none";
//     Tre.style.display="none";
//     NUGSS.style.display="none";
//     NUGST.style.display="flex";
// })