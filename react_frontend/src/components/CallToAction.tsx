import { ArrowRight } from 'lucide-react';

interface CallToActionProps {
  onGetStarted: () => void;
}

export function CallToAction({ onGetStarted }: CallToActionProps) {
  return (
    <section className="container mx-auto px-6 py-20">
      <div className="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl overflow-hidden">
        <div className="px-8 py-16 md:px-16 md:py-20 text-center">
          <h2 className="text-white mb-4">
            Ready to Transform Your Business?
          </h2>
          <p className="text-blue-100 mb-8 max-w-2xl mx-auto">
            Join hundreds of small businesses that are already using CorsaireCRM to streamline their operations and grow faster.
          </p>
          
          <div className="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <button
              onClick={onGetStarted}
              className="px-8 py-4 bg-white text-blue-600 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2"
            >
              Start Free Trial
              <ArrowRight className="w-5 h-5" />
            </button>
            <button className="px-8 py-4 border-2 border-white text-white rounded-lg hover:bg-white/10 transition-colors">
              Schedule a Demo
            </button>
          </div>

          <p className="text-blue-100 mt-6">
            No credit card required • 14-day free trial • Cancel anytime
          </p>
        </div>
      </div>
    </section>
  );
}