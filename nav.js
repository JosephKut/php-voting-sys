function voters(){
    document.getElementById('dash').style.display="none";
    document.getElementById('voters').style.display="flex";
}

function not_sent(){
    document.getElementById('dash').style.display="none";
    document.getElementById('not_sent').style.display="flex";
}

function voted_info(){
    document.getElementById('dash').style.display="none";
    document.getElementById('voted').style.display="flex";
}

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

function checked(){
    ps.style.display="none";
    Pre.style.display="none";
}

const contents =document.getElementsByClassName('content');
const dash=document.getElementById('dash');
const postM=document.getElementById('postM');
const candidateM=document.getElementById('candidateM');
//const deptM=document.getElementById('deptM');
const viewC=document.getElementById('viewC');
const viewR=document.getElementById('viewR');
//const NB=document.getElementById('NB');
const ST=document.getElementById('ST');

function Fdash(){
    for (let i = 0; i<contents.length; i++){
            contents[i].style.display="none";
        }
    dash.style.display="flex";
}

function FpostM(){
    for (let i = 0; i<contents.length; i++){
            contents[i].style.display="none";
        }
    postM.style.display="flex";
}

function FcandidateM(){
    for (let i = 0; i<contents.length; i++){
            contents[i].style.display="none";
        }
    candidateM.style.display="flex";
}


function FviewC(){
    for (let i = 0; i<contents.length; i++){
            contents[i].style.display="none";
        }
    viewC.style.display="flex";
}

function FviewR(){
    for (let i = 0; i<contents.length; i++){
            contents[i].style.display="none";
        }
    viewR.style.display="flex";
}

function FST(){
    for (let i = 0; i<contents.length; i++){
            contents[i].style.display="none";
        }
    ST.style.display="flex";
}

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

for (let n = 0; n<content.length; n++){
    document.getElementById(postButtonID[n]).addEventListener('click', function(){
        for (let i = 0; i<content.length; i++){
            content[i].style.display="none";
        }
        document.getElementById(postContentID[n]).style.display="flex";
    });
}
