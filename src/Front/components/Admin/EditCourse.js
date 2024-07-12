import { useParams } from "react-router";
import { useQuery } from 'react-query';
import { useForm } from "react-hook-form";
import React, { useEffect, useState } from 'react';
import { __, sprintf } from '@wordpress/i18n';
import { Button, Input, Select, Textarea, VStack, FormErrorMessage, FormControl, FormLabel, useToast, Image } from "@chakra-ui/react";
import { Link } from "react-router-dom";

const EditCourse = () => {
  const { id } = useParams();
  const [imagePreview, setImagePreview] = useState(null);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const fetchCourseById = async () => {
    const res = await fetch(`http://tg-lms.local:10010/wp-json/tg-course/v1/course/${id}`);
    if (!res.ok) {
      showToast({ title: "Failed to get course", description: "Please Try Again later", status: "error" });
      throw new Error("Network response was not ok");
    }
    return res.json();
  };

  const { isLoading, error, data } = useQuery(["fetch_course_by_id", id], fetchCourseById, {
    enabled: !!id,
  });


  console.log(data,'edit data');
  const { register, handleSubmit, setValue, formState: { errors }, setError } = useForm();
  const toast = useToast();

  const showToast = ({ title, description, status }) => {
    toast({
      title: sprintf(__('%s', 'tg-lms'), title),
      description: sprintf(__('%s', 'tg-lms'), description),
      status: status,
      duration: 3000,
      isClosable: true,
    });
  };

  useEffect(() => {
    if (data) {
      setValue("course_title", data.post_title);
      setValue("course_description", data.post_content);
      setValue("course_price", data.meta_data?.course_price[0]);
      setValue("course_category", data?.post_excerpt);
      setValue("course_status", data.meta_data?.course_status[0]);
      setImagePreview(data.meta_data?.course_image_url); 
    }
  }, [data, setValue]);

  const fetchAllCategories = async () => {
    const res = await fetch("http://tg-lms.local:10010/wp-json/tg-course/v1/categories");
    if (!res.ok) {
      showToast({ title: "Failed to get categories", description: "Please Try Again later", status: "error" });
      throw new Error("Network response was not ok");
    }
    return res.json();
  };

  const { isLoading: isCategoriesLoaded, error: categoriesError, data: allCategories } = useQuery("categories", fetchAllCategories);

  const submitHandler = async (userInput) => {
    userInput.course_id = id;
    setIsSubmitting(true)
    const formData = new FormData();

    for (let key in userInput) {
      if (key) {
        formData.append(key, userInput[key]);
      }
    }

    if (userInput.course_image[0]) {
      formData.append("course_image", userInput.course_image[0]);
    }

    console.log(formData)

    try {
      const res = await fetch(`http://tg-lms.local:10010/wp-json/tg-course/v1/course/edit/${id}`, {
        method: 'PUT',
        body: formData
      });



      if (!res.ok) {
        setIsSubmitting(false)
        showToast({ title: "Failed to update course", description: "Please Try with Correct Input", status: "error" });
        throw new Error('Failed to update course');
      }

      const data = await res.json();
      setIsSubmitting(false);
      showToast({ title: "Update Successful", description: "Course Updated Successfully", status: "success" });
    } catch (error) {
      
      showToast({ title: "Failed to update course", description: `${error?.message}`, status: "error" });
      setError("api", {
        type: "manual",
        message: error.message
      });
    }finally{
      setIsSubmitting(false)
    }
  };

  return (
    <VStack className='w-full py-8 px-2'>
      <div className='flex py-4 justify-center items-center w-full'>
                      <Link to={`/courses`}>
                <Button className='' disabled={isLoading}>
          {'<< All Courses'}
        </Button>
              </Link>
        <h1 className='mx-auto text-3xl'>{__('Edit Course', 'tg-lms')}</h1>
      </div>

      {isLoading && <p>Loading...</p>}
      {error && showToast({ title: "Failed to Load course", description: "Please Try Again", status: "error" })}

      {!isLoading && data && (
        <form className='flex flex-col gap-4 w-[50%]' onSubmit={handleSubmit(submitHandler)} encType="multipart/form-data">
          <VStack>
            <FormControl isInvalid={errors.course_title}>
              <FormLabel>{__('Course Title', 'tg-lms')}</FormLabel>
              <Input
                {...register("course_title", { required: __('Course title is required', 'tg-lms') })}
                type='text'
                placeholder={__('Course Title', 'tg-lms')}
              />
              <FormErrorMessage>{errors.course_title && errors.course_title.message}</FormErrorMessage>
            </FormControl>

            <FormControl isInvalid={errors.course_description}>
              <FormLabel>{__('Course Description', 'tg-lms')}</FormLabel>
              <Textarea
                {...register("course_description", { required: __('Course description is required', 'tg-lms') })}
                rows={4}
                cols={5}
                placeholder={__('Course Description', 'tg-lms')}
              />
              <FormErrorMessage>{errors.course_description && errors.course_description.message}</FormErrorMessage>
            </FormControl>

            <FormControl isInvalid={errors.course_image}>
              <FormLabel>{__('Select Image', 'tg-lms')}</FormLabel>
              {imagePreview && <Image src={imagePreview} alt={__('Course Image', 'tg-lms')} boxSize="200px" objectFit="cover" mb={4} />}
              <Input
                {...register("course_image")}
                type='file'
              />
              <FormErrorMessage>{errors.course_image && errors.course_image.message}</FormErrorMessage>
            </FormControl>

            <FormControl isInvalid={errors.course_price}>
              <FormLabel>{__('Price', 'tg-lms')}</FormLabel>
              <Input
                {...register("course_price", { required: __('Course price is required', 'tg-lms') })}
                type='number'
                placeholder={__('Price', 'tg-lms')}
              />
              <FormErrorMessage>{errors.course_price && errors.course_price.message}</FormErrorMessage>
            </FormControl>

            <FormControl isInvalid={errors.course_category}>
              <FormLabel>{__('Choose Category', 'tg-lms')}</FormLabel>
              <Select {...register("course_category", { required: __('Course category is required', 'tg-lms') })}>
                {isCategoriesLoaded && <option>Loading...</option>}
                {allCategories && allCategories.map(i => (
                  <option key={i} value={i}>{sprintf(__('%s', 'tg-lms'), i)}</option>
                ))}
              </Select>
              <FormErrorMessage>{errors.course_category && errors.course_category.message}</FormErrorMessage>
            </FormControl>

            <FormControl isInvalid={errors.course_status}>
              <FormLabel>{__('Choose Status', 'tg-lms')}</FormLabel>
              <Select {...register("course_status", { required: __('Course status is required', 'tg-lms') })}>
                <option value="">{__('Choose Status', 'tg-lms')}</option>
                <option value="paid">{__('Paid', 'tg-lms')}</option>
                <option value="free">{__('Free', 'tg-lms')}</option>
              </Select>
              <FormErrorMessage>{errors.course_status && errors.course_status.message}</FormErrorMessage>
            </FormControl>

            {errors.api && <FormErrorMessage>{errors.api.message}</FormErrorMessage>}

            <Button type="submit" colorScheme="blue" className='text-left'>
              { !isSubmitting ?  __('Edit Course', 'tg-lms') : __('Submitting', 'tg-lms')}
            </Button>
          </VStack>
        </form>
      )}
    </VStack>
  );
};

export default EditCourse;
