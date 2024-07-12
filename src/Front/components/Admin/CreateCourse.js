import { useForm } from "react-hook-form";
import React, { useState } from 'react';
import { __ ,sprintf} from '@wordpress/i18n';  // Importing translation function
import { Button, Input, Select, Textarea, VStack, FormErrorMessage, FormControl, FormLabel } from "@chakra-ui/react";
import { useToast } from "@chakra-ui/react";
import { useQuery } from "react-query";
import { Link } from "react-router-dom";
import { Image } from "@chakra-ui/react";

const CreateCourse = () => {
  const [isCreating, setIsCreating] = useState(false);
  
  const [file,setFile] = useState(null);
  const [preview, setPreview] = useState(null)

  const { register, handleSubmit, formState: { errors }, setError } = useForm();
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

  const fetchAllCategories = async () => {
    const res = await fetch("http://tg-lms.local:10010/wp-json/tg-course/v1/categories");
    if (!res.ok) {
      throw new Error("Network response was not ok");
    }
    return res.json();
  };

  const { isLoading, data: allCategories } = useQuery("categories", fetchAllCategories);


    const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
      setFile(file);
      setPreview(URL.createObjectURL(file));
    } else {
      setFile(null);
      setPreview(null);
    }
  };

  const submitHandler = async (userInput) => {
    setIsCreating(true);

    const formData = new FormData();
    for (let key in userInput) {
      if (key) {
        formData.append(key, userInput[key]);
      }
    }
    if(file){
      formData.append("course_image", file);

    }

    if (userInput.course_image[0]) {
    }

    try {
      const res = await fetch(`http://tg-lms.local:10010/wp-json/tg-course/v1/course`, {
        method: 'POST',
        body: formData
      });

      if (!res.ok) {
        setIsCreating(false);
        showToast({title: "Failed to create course", description: "Please Try Again later", status: "error"});
      }

      const data = await res.json();
      showToast({title: sprintf(__('%s', 'tg-lms'), `${data.message}`), description: "Successfully Created Course.", status: "success"});
    } catch (error) {
      return showToast({title: sprintf(__('%s', 'tg-lms'), "Create Course Failed !"), description: sprintf(__('%s', 'tg-lms'), error?.message), status: "error"});
    } finally {
      setIsCreating(false);
    }
  };

  return (
    <VStack className='w-full py-8 px-2'>
      <div className='flex py-4 gap-14 justify-center items-center w-full'>
              <Link to={`/courses`}>
                <Button className='' disabled={isLoading}>
          {'<< All Courses'}
        </Button>
              </Link>
        <h1 className='mx-auto text-3xl'>{__('Create Course', 'tg-lms')}</h1>
      </div>

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
            <Input
              {...register("course_image", { required: __('Course image is required', 'tg-lms') })}
              type='file'
              onChange={handleImageChange}
            />
            {preview && <Image src={preview} alt="Preview" mt={4} boxSize="200px" />}
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
              {isLoading && <option>Loading...</option>}
              {allCategories?.map(i => (
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
            {isCreating ? __('Creating...', 'tg-lms') : __('Create Course', 'tg-lms')}
          </Button>
        </VStack>
      </form>
    </VStack>
  );
};

export default CreateCourse;
