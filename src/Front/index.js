// import React from "react";
// import ReactDOM from 'react-dom/client'
// import AllCourse from "./components/Admin/AllCourse";
// import CreateCourse from "./components/Admin/CreateCourse";
// import './index.css'
// document.addEventListener("DOMContentLoaded", function() {
//     const domNode = document.getElementById("tg-root");
//     const pageSlug = domNode.dataset.page
//     const root = ReactDOM.createRoot(domNode)
//     let activeComponent;
//     switch(pageSlug){
//         case 'all-course':
//             activeComponent = <AllCourse/>
//             break;
            
//             case 'new-course':
//             activeComponent = <CreateCourse/>
//             break;

//         default:
//             activeComponent = <h1>Loading...</h1>
//             break;
//     }

//     root.render(activeComponent);

// });










import React from "react";
import ReactDOM from 'react-dom/client'
import App from "./App";
import './index.css';

document.addEventListener("DOMContentLoaded", function() {
    const domNode = document.getElementById("tg-root");
    const root = ReactDOM.createRoot(domNode)
    root.render(<App/>);

});