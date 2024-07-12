import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { useToast } from '@chakra-ui/react';
import { VStack, Text, Box, Image, Badge, Skeleton, Flex } from '@chakra-ui/react';
import { __, sprintf } from '@wordpress/i18n';

const SingleCourse = () => {
  const { id } = useParams();
  const [course, setCourse] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const toast = useToast();

  useEffect(() => {
    if (id) {
      fetchCourseById(id);
    }
  }, [id]);

  const fetchCourseById = async (courseId) => {
    try {
      const res = await fetch(`http://tg-lms.local:10010/wp-json/tg-course/v1/course/${courseId}`);
      if (!res.ok) {
        throw new Error('Network response was not ok');
      }
      const data = await res.json();
      setCourse(data);
    } catch (error) {
      toast({
        title: sprintf(__('%s', 'tg-lms'), 'Failed to load course'),
        description: sprintf(__('%s', 'tg-lms'), error.message),
        status: 'error',
        duration: 3000,
        isClosable: true,
      });
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <VStack className='w-full py-8 px-4'>
      <Skeleton isLoaded={!isLoading} className='w-full flex flex-col gap-4'>
        {course && (
          <>
            <Flex direction='column' gap='4'>
              <Text as='h1' fontSize='3xl' fontWeight='bold'>
                {course.title}
              </Text>
              <Text fontSize='xl'>{course.description}</Text>
              <Box>
                <Badge colorScheme='yellow' fontSize='lg' px='4' py='1'>
                  {__('Best Seller', 'tg-lms')}
                </Badge>
              </Box>
            </Flex>
            <Box className='w-full h-[30rem]'>
              <Image className='w-full h-full object-cover' src={course.image} alt={course.title} />
            </Box>
            <Text fontSize='md'>{course.content}</Text>
          </>
        )}
        {!course && !isLoading && (
          <Text fontSize='xl' color='red.500'>
            {__('Course not found', 'tg-lms')}
          </Text>
        )}
      </Skeleton>
    </VStack>
  );
};

export default SingleCourse;
