import React, { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from 'react-query';
import {
  Table,
  Thead,
  Tbody,
  Tr,
  Th,
  Td,
  TableCaption,
  TableContainer,
  Spinner,
  Select,
  useToast,
  Button,
  Box,
  HStack,
  Input
} from '@chakra-ui/react';
import { FaEdit } from "react-icons/fa";
import { MdDelete } from "react-icons/md";
import { Link } from 'react-router-dom';
import { __, sprintf } from '@wordpress/i18n'; 

const CourseHeader = ({ onSearch, onCategoryChange, onPriceChange, categories }) => {
  return (
    <div className='flex w-[80%] mx-auto justify-center items-center gap-8 rounded-3xl'>
      <form className="max-w-md flex gap-2 mx-auto" onSubmit={onSearch}>
        <label className="input flex items-center gap-2">
          <input type="text" className="grow" placeholder="Search" name="search" />
          <svg xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 16 16"
            fill="currentColor"
            className="h-4 w-4 opacity-70">
            <path
              fillRule="evenodd"
              d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
              clipRule="evenodd" />
          </svg>
        </label>
      </form>
      <div>
        <select className="select select-primary w-full max-w-xs" onChange={onCategoryChange} defaultValue="">
          <option value="" disabled>Select Category</option>
          {
            categories?.map(i => (
              <option key={i} value={i}>{sprintf(__('%s', 'tg-lms'), i)}</option>
            ))
          }
        </select>
      </div>
      <div>
        <select className="select select-primary w-full max-w-xs" onChange={onPriceChange} defaultValue="">
          <option value="" disabled>Select Price Type</option>
          <option value="paid">{sprintf(__('%s', 'tg-lms') ,"paid")}</option>
          <option value="free">{sprintf(__('%s', 'tg-lms') ,"free")}</option>
        </select>
      </div>
    </div>
  );
}

const Pagination = ({ currentPage, totalPages, onPageChange }) => {
  return (
    <HStack spacing={2} justify="center" mt={4}>
      <Button
        onClick={() => onPageChange(currentPage - 1)}
        isDisabled={currentPage === 1}
      >
        Previous
      </Button>
      <Box>
        Page {currentPage} of {totalPages}
      </Box>
      <Button
        onClick={() => onPageChange(currentPage + 1)}
        isDisabled={currentPage === totalPages}
      >
        Next
      </Button>
    </HStack>
  );
};

const AllCourse = () => {
  const queryClient = useQueryClient();
  const toast = useToast();
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  const showToast = ({ title, description, status }) => {
    toast({
      title: sprintf(__('%s', 'tg-lms'), title),
      description: sprintf(__('%s', 'tg-lms'), description),
      status: status,
      duration: 3000,
      isClosable: true,
    });
  };

  const fetchAllCategories = async () => {
    const res = await fetch("http://tg-lms.local:10010/wp-json/tg-course/v1/categories");
    if (!res.ok) {
      throw new Error("Network response was not ok");
    }
    return res.json();
  };

  const { isLoading: isCategoryLoading, error: categoryError, data: allCategories } = useQuery("categories", fetchAllCategories);

  const fetchCourses = async ({ queryKey }) => {
    const [, page] = queryKey;
    const res = await fetch(`http://tg-lms.local:10010/wp-json/tg-course/v1/course?page=${page}`);
    if (!res.ok) {
      throw new Error("Network response was not ok");
    }
    const data = await res.json();
    setTotalPages(data.totalPages);
    return data;
  };

  const { isLoading, error, data } = useQuery(["courses", currentPage], fetchCourses, {
    keepPreviousData: true,
  });

  const sortByTitleHandler = async () => {
    const res = await fetch(`http://tg-lms.local:10010/wp-json/tg-course/v1/courses/sort/`);
    if (!res.ok) {
      throw new Error(`Network response was not ok`);
    }
    return res.json();
  };

  const mutation = useMutation(sortByTitleHandler, {
    onSuccess: () => {
      queryClient.invalidateQueries('courses');
    },
  });

  const filterByCategoryHandler = async (category) => {
    const res = await fetch(`http://tg-lms.local:10010/wp-json/tg-course/v1/courses/filter?cat=${category}`);
    if (!res.ok) {
      throw new Error(`Network response was not ok`);
    }
    return res.json();
  };

  const filterByPriceTypeHandler = async (price_type) => {
    const res = await fetch(`http://tg-lms.local:10010/wp-json/tg-course/v1/courses/filter/status?price_type=${price_type}`);
    if (!res.ok) {
      throw new Error(`Network response was not ok`);
    }
    return res.json();
  };

  const searchByTitleHandler = async (searchQuery) => {
    const res = await fetch(`http://tg-lms.local:10010/wp-json/tg-course/v1/courses/search?q=${searchQuery}`);
    if (!res.ok) {
      throw new Error(`Network response was not ok`);
    }
    return res.json();
  };

  const deleteCourseHandler = async (courseId) => {
    const res = await fetch(`http://tg-lms.local:10010/wp-json/tg-course/v1/course/${courseId}`, {
      method: 'DELETE'
    });
    if (!res.ok) {
      throw new Error(`Network response was not ok`);
    }
    return res.json();
  };

  const { mutate: deleteCourse } = useMutation(deleteCourseHandler, {
    onSuccess: () => {
      showToast({
        title: sprintf(__('%s', 'tg-lms'), "Delete Successful"),
        description: "Course Successfully deleted",
        status: "success",
      });
      queryClient.invalidateQueries('courses');
    },
    onError: (error) => {
      showToast({
        title: sprintf(__('%s', 'tg-lms'), error?.message),
        description: "Failed to delete",
        status: "error",
      });
    },
  });

  const handleSearch = (event) => {
    event.preventDefault();
    const searchQuery = event.target.search.value;
    searchByTitleHandler(searchQuery).then(data => {
      queryClient.setQueryData('courses', data);
      queryClient.invalidateQueries('courses');
    });
  };

  const handlePriceChange = (event) => {
    const price_type = event.target.value;
    filterByPriceTypeHandler(price_type).then(data => {
      queryClient.setQueryData('courses', data);
      queryClient.invalidateQueries('courses');
    });
  };

  const handleCategoryChange = (event) => {
    const category = event.target.value;
    filterByCategoryHandler(category).then(data => {
      queryClient.setQueryData('courses', data);
      queryClient.invalidateQueries('courses');
    });
  };

  const handlePageChange = (page) => {
    setCurrentPage(page);
  };

  if (categoryError) {
    showToast({ title: "Fetch Categories Failed", description: categoryError?.message || 'Please Try Again', status: "error" });
  }

  if (error) {
    showToast({ title: "Fetch Courses Failed", description: error.message || 'Please Try Again', status: "error" });
  }

  return (
    <div className='flex pt-[3rem] flex-col gap-4'>
      <CourseHeader categories={allCategories} onSearch={handleSearch} onCategoryChange={handleCategoryChange} onPriceChange={handlePriceChange} />
      <TableContainer>
        <h1 className='text-3xl font-semibold text-center py-5'>All Courses</h1>
             
             <Link  to={`/new-course`}>
        <Button  className='' disabled={isLoading}>
          Add New Course
        </Button>
             </Link>
        {isLoading ? (
          <div className='w-full mx-auto flex justify-center items-center'>
            <Spinner className='mx-auto' size="xl" />
          </div>
        ) : (
          <Table variant='simple'>
            <TableCaption>All available courses</TableCaption>
            <Thead>
              <Tr>
                <Th>ID</Th>
                <Th>Image</Th>
                <Th onClick={() => mutation.mutate()}>Title</Th>
                <Th>Price</Th>
                <Th>Status</Th>
                <Th>Price Type</Th>
                <Th>Category</Th>
                <Th>Edit</Th>
                <Th>Delete</Th>
              </Tr>
            </Thead>
            <Tbody>
              {data?.map(course => (
                <Tr className='' key={course?.ID}>
                  <Td>{course?.ID}</Td>
                  <Td>{course?.meta_data?.course_image[0]}</Td>
                  <Link to={`/course/${course?.ID}`}>
                      {course?.post_title}
                   </Link>
                  <Td>{course?.meta_data?.course_price[0]}</Td>
                  <Td>{course?.post_status}</Td>
                  <Td>{course?.meta_data?.course_status[0]}</Td>
                  <Td>{course?.post_excerpt}</Td>
                  <Td>
                    <Link to={`/course/edit/${course?.ID}`}>
                      <FaEdit className='w-[2rem] h-[2rem] text-blue-400' />
                    </Link>
                  </Td>
                  <Td>
                    <MdDelete onClick={() => deleteCourse(course?.ID)} className='w-[2rem] cursor-pointer active:text-red-600 h-[2rem] text-red-400' />
                  </Td>
                </Tr>
              ))}
            </Tbody>
          </Table>
        )}
      </TableContainer>
      <Pagination currentPage={currentPage} totalPages={totalPages} onPageChange={handlePageChange} />
    </div>
  );
};

export default AllCourse;
