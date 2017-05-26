let store = {};
let base_url = window.Laravel.appPath;

export default store

// are we in test mode?
store.inDemoMode = false;
store.loadingThreads = false;
store.threadsIncomplete = true;

// step
store.currentStep = 'step-one';

// member data
store.member_id = '';
store.ingame_name = '';
store.forum_name = '';
store.platoon = '';
store.squad = '';

// division data
store.division = {
    abbreviation: '',
    settings: [],
    platoons: [],
    squads: [],
    threads: [],
    tasks: []
};

// locality data
store.locality = {
    platoon: 'Platoon',
    platoons: 'Platoons',
    squad: 'Squad',
    squads: 'Squads',
};

/**
 * fetches platoons for recruiting
 *
 * @param division
 */
store.getPlatoons = (division) => {
    axios.get(base_url + '/division-platoons/' + division)
        .then(function (response) {
            store.division.platoons = response.data.data.platoons;
            store.division.settings = response.data.data.settings;
        })
        .catch(function (error) {
            toastr.error(error, 'Something went wrong!')
        });
};

/**
 * has the recruit responded to all threads?
 *
 * @param threads
 */
store.checkIfIncomplete = function (threads) {
    threads.forEach(function (thread) {
        if (!thread.status) {
            store.threadsIncomplete = true;
            return;
        }
        store.threadsIncomplete = false;
    })
};

/**
 * fetches a division's required agreement threads
 *
 * @param division
 */
store.getDivisionThreads = (division) => {
    store.loadingThreads = true;
    axios.post(base_url + '/search-division-threads', {
        division: division,
        string: store.member_id,
        isTesting: store.inDemoMode,
    }).then(function (response) {
        store.loadingThreads = false;
        store.division.threads = response.data;
        store.checkIfIncomplete(store.division.threads);
    }).catch(function (error) {
        toastr.error(error, 'Something went wrong!')
    });
};

/**
 * fetches a platoon's squads
 *
 * @param platoon
 */
store.getPlatoonSquads = (platoon) => {
    axios.post(base_url + '/platoon-squads/', {
        platoon: platoon
    }).then(function (response) {
        store.division.squads = response.data;
    }).catch(function (error) {
        toastr.error(error, 'Something went wrong!')
    });
};

/**
 * fetch a division's recruiting tasks
 *
 * @param division
 */
store.getTasks = (division) => {
    axios.post(base_url + '/division-tasks/', {
        division: division
    }).then(function (response) {
        store.division.tasks = response.data;
    }).catch(function (error) {
        toastr.error(error, 'Something went wrong!')
    });
};

/**
 * pushes a request to create a new member
 */
store.createMember = () => {
    axios.post(base_url + '/add-member/', {
        division: store.division.abbreviation,
        member_id: store.member_id,
        forum_name: store.forum_name,
        ingame_name: store.ingame_name,
        platoon: store.platoon,
        squad: store.squad
    }).then(function (response) {
        toastr.success('Your recruit has been added to the tracker');
    }).catch(function (error) {
        toastr.error(error, 'Something went wrong!')
    });
};