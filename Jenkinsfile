node {
     stage('Build') {
        echo 'Install composer files'
        sh 'composer install'
        echo 'Build Kunena Package files'
        sh 'vendor/bin/phing -f build/phing/build.xml'
        echo 'Sync Completed'
     }

    stage('Test') {
        echo 'Start Selenium test with firefox'
        sh 'vendor/bin/robo run:tests'
        echo 'test Completed'
    }

    stage('Deploy') {
        echo 'Deploy Todo'
    }
}
