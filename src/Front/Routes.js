import React from 'react'
import { HashRouter, Routes, Route } from "react-router-dom"; // Ensure correct import
import AllCourse from './components/Admin/AllCourse';
import SingleCourse from './components/Admin/SingleCourse';
import CreateCourse from './components/Admin/createCourse';
import EditCourse from './components/Admin/EditCourse';

const CRoutes = () => {
  return (
        <HashRouter>
        <Routes>
          <Route path="/new-course" element={<CreateCourse/>} />
          <Route path="/courses" element={<AllCourse/>} />
          <Route path="/course/edit/:id" element={<EditCourse/>} />
          <Route path="/courses/:id" element={<SingleCourse/>}/>
        </Routes>
      </HashRouter>
  )
}

export default CRoutes