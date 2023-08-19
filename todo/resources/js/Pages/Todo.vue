<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Dropdown from '@/Components/Dropdown.vue';


// reactive props
const props = defineProps({
    todos: {
        type: Object,
        default: null
    }
})

// reactive form using vue3 composition api and inertiajs
const form = useForm({
    content: '',
})
// reactive editing todo to show update button and update todo
const editingTodo = ref(false);

// reactive checked todos to keep track of checked todos
const checked = ref([]);

/**
 * reset checked todos
 */
function resetChecked() {
    checked.value = [];
}

// reactive allChecked to check all todos
const allChecked = ref(false);

/**
 * submit form
 */
function submit() {
    // if editing todo is true then update todo
    // else create new todo
    if (editingTodo.value) {
        // update todo
        form.patch(route('todos.update', editingTodo.value), {
            onSuccess: () => {
                // reset form and editing todo
                form.reset();
                editingTodo.value = false;
            },
            preserveScroll: true,
            preserveState: true,
            only: ['todos']
        })
    } else {
        // create new todo
        form.post(route('todos.store'), {
            onSuccess: () => {
                // reset form
                form.reset();

                // reset checked todos and allChecked
                resetChecked();
                allChecked.value = false;
            },
            preserveScroll: true,
            preserveState: true,
            only: ['todos']
        })
    }
}

/**
 * change form content to todo content
 * set editing todo to todo id
 * @param {*} todo 
 */
function edit(todo) {
    editingTodo.value = todo.id;
    form.content = todo.content;
}

/**
 * watch allChecked
 */
watch(allChecked, (newValue, oldValue) => {
    // empty checked todos
    resetChecked()

    // if allChecked is true then push all todos id to checked todos
    if (newValue) {
        // push all todos id to checked todos
        for (const todo of props.todos.data) {
            checked.value.push(todo.id);
        }
    }
})

/**
 * delete all checked todos
 */
function deleteMarked() {
    router.delete(route('todos.destroy-many', { todos: checked.value }),
    {
        preserveScroll: true, preserveState: true,
        onSuccess: () => {
            // empty checked todos
            resetChecked()
        }
    })
}

/**
 * mark all checked todos as complete
 */
function completeMarked() {
    router.patch(route('todos.complete-many'), { todos: checked.value, status: 'completed' }, {
        preserveScroll: true, preserveState: true,
        onSuccess: () => {
            // empty checked todos
            resetChecked()
        }
    })
}


/**
 * delete todo
 * @param {*} todo 
 */
function deleteTodo(todo) {
    router.delete(route('todos.destroy', todo), {
        onSuccess: () => {
            // remove todo from checked todos
            checked.value = checked.value.filter(t => t != todo.id)
        },
        preserveScroll: true, preserveState: true
    })
}
/**
 * delete all completed todos
 */
function deleteCompleted() {
    router.delete(route('todos.destroy-completed'), {
        onSuccess: () => {
        },
        preserveScroll: true, preserveState: true
    })
}

/**
 * cancel editing todo
 */
function cancelEdit() {
    form.reset();
    editingTodo.value = null;
}

</script>

<template>
    <Head>
        <title>Todo App</title>
    </Head>

    <Layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight custom-class">Todo</h2>
        </template>

        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
                <div>
                    <!-- todo creation form -->
                    <div class="w-full my-6 px-6 py-4 bg-white shadow-sm overflow-hidden rounded-md">
                        <form @submit.prevent="submit" class="flex gap-4">
                            <div class="w-full">
                                <InputLabel for="content" value="Content" />
                                <TextInput id="content" type="text" class="mt-1 block w-full" v-model="form.content"
                                    required placeholder="Enter content.." autocomplete="content" />
                                <InputError class="mt-2" :message="form.errors.content" />
                            </div>
                            <div class="whitespace-nowrap mt-6">
                                <!-- submit button to submit form -->
                                <PrimaryButton class="py-3" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing">
                                    <!-- update todo if editing todo is true else create new todo -->
                                    {{ editingTodo ? 'Update Todo' : 'Add New Todo' }}
                                </PrimaryButton>
                                <PrimaryButton class="py-3 ml-4" type="button" @click="cancelEdit">
                                    <!-- reset or cancel form -->
                                    {{ editingTodo ? 'Cancel' : 'Reset' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>

                    <!-- table to show the todos. -->
                    <div class="shadow-sm rounded-lg bg-white overflow-hidden">
                        <table class="w-full overflow-hidden">
                            <thead class="bg-blue-500 text-white divide-x border-b">
                                <tr>
                                    <th class="p-4 text-left">
                                        <Checkbox :checked="false" v-model="allChecked" />
                                    </th>
                                    <th class="p-4 text-left">ID</th>
                                    <th class="p-4 text-left">Content</th>
                                    <th class="p-4 text-left">Status</th>
                                    <th class="text-left">
                                        <div class="flex justify-between mx-2">
                                            <span class="py-2">Actions</span>
                                            <!-- a button manage multiple todo at once -->
                                            <Dropdown>
                                                <template #trigger>
                                                    <button type="button" class="p-2">
                                                        <svg stroke="currentColor"
                                                            class="h-6 w-6 hover:fill-gray-300 hover:rotate-90 transition-all fill-white"
                                                            viewBox="0 0 512 512">
                                                            <path
                                                                d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z" />
                                                        </svg>
                                                    </button>
                                                </template>
                                                <template #content>
                                                    <div class="p-3 flex flex-col gap-4">
                                                        <!-- delete all completed todos. -->
                                                        <PrimaryButton type="button" @click="deleteCompleted"
                                                            class="justify-center">
                                                            Delete Completed
                                                        </PrimaryButton>
                                                        <!-- mark all the checked todos as complete -->
                                                        <PrimaryButton type="button" @click="completeMarked"
                                                            class="justify-center" :disabled="checked.length == 0">
                                                            Complete Marked
                                                        </PrimaryButton>
                                                        <!-- delete all the checked todos. -->
                                                        <DangerButton type="button" @click="deleteMarked"
                                                            class="justify-center" :disabled="checked.length == 0">
                                                            Delete Marked
                                                        </DangerButton>
                                                    </div>
                                                </template>
                                            </Dropdown>

                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <!-- use a transition group to animate the list -->
                            <transition-group class="divide-y divide-solid" tag="tbody" name="list">
                                <!-- show todos if todos length is greater than 0 -->
                                <template v-if="todos.data.length">
                                    <tr v-for="todo in todos.data" :key="todo.id" class="hover:bg-gray-200">
                                        <td class="p-4 font-bold">
                                            <!-- a checkbox to check todo for multiple delete -->
                                            <Checkbox :value="todo.id" :checked="checked" v-model="checked" />
                                        </td>
                                        <td class="p-4 font-bold">#{{ todo.id }}</td>
                                        <td class="p-4 w-full">{{ todo.content }}</td>
                                        <td class="p-4">
                                            <!-- show todo status -->
                                            <span class="text-xs rounded-md px-2 py-1 text-white font-semibold"
                                                :class="{ 'bg-emerald-500': todo.status == 'completed', 'bg-orange-500': todo.status == 'incomplete', }">
                                                {{ todo.status }}
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex gap-2 justify-end">
                                                <!-- a button to complete todo -->
                                                <PrimaryButton type="button"
                                                    @click="router.patch(route('todos.update', todo), { status: 'completed' })"
                                                    v-if="todo.status != 'completed'"
                                                    class="bg-green-500 text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900">
                                                    Complete
                                                </PrimaryButton>
                                                <!-- a button to edit todo -->
                                                <PrimaryButton type="button" @click="edit(todo)">
                                                    Edit
                                                </PrimaryButton>
                                                <!-- a button to delete todo -->
                                                <DangerButton type="button" @click="deleteTodo(todo)">
                                                    Delete
                                                </DangerButton>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <!-- show not found if todos length is 0 -->
                                <template v-else>
                                    <tr key="not-found">
                                        <td colspan="5" class="p-4 text-center text-lg font-bold">No todo left.</td>
                                    </tr>
                                </template>
                                <!-- Pagination -->
                                <tr key="pagination">
                                    <td colspan="5" class="p-4">
                                        <nav aria-label="Page navigation example">
                                            <ul class="flex justify-end">
                                                <li v-for="link in todos.links">
                                                    <Link :class="{ 'bg-blue-500 text-white': link.active }"
                                                        v-if="link.url != null"
                                                        class="block rounded px-3 py-1.5 transition-all duration-300 hover:bg-neutral-300"
                                                        :href="link.url" v-html="link.label">
                                                    </Link>
                                                    <span v-else
                                                        class="block rounded px-3 py-1.5 transition-all duration-300 cursor-not-allowed"
                                                        v-html="link.label"></span>
                                                </li>
                                            </ul>
                                        </nav>
                                    </td>
                                </tr>
                            </transition-group>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style>
/* animation for the transition-group */
.list-enter-active {
    @apply transition-all duration-300 ease-in;
}

.list-leave-active {
    @apply transition-all duration-300 absolute max-w-7xl delay-100 ease-out;
}

.list-move {
    @apply transition-all duration-300 ease-linear delay-100;
}

.list-enter-from,
.list-leave-to {
    @apply opacity-0 scale-50;
}</style>